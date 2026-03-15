<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * TransactionService — contiene toda la lógica de negocio
 * relacionada con transacciones.
 *
 * El controlador delega aquí. Así si en el futuro un Job de
 * importación CSV necesita crear transacciones, usa este mismo
 * servicio sin duplicar código.
 */
class TransactionService
{
    /**
     * Devuelve los datos formateados para DataTables server-side.
     *
     * DataTables envía estos parámetros en cada petición:
     *   - start:          offset (desde qué fila empezar)
     *   - length:         cuántas filas devolver
     *   - search[value]:  término de búsqueda global
     *   - order[0][column] y order[0][dir]: columna y dirección de orden
     *
     * Debemos devolver:
     *   - draw:            el mismo valor que llegó (seguridad anti-XSS de DataTables)
     *   - recordsTotal:    total de registros sin filtrar
     *   - recordsFiltered: total después de aplicar búsqueda
     *   - data:            array de filas para la página actual
     */
    public function getDataTablesData(Request $request, int $userId): array
    {
        // Columnas que DataTables puede ordenar, mapeadas por índice.
        // Solo permitimos columnas de nuestra whitelist — nunca usamos
        // el valor raw del request para evitar SQL injection.
        $columns = [
            0 => 'transactions.date',
            1 => 'accounts.name',
            2 => 'categories.name',
            3 => 'transactions.merchant',
            4 => 'transactions.amount',
            5 => 'transactions.type',
        ];

        // Query base — siempre filtramos por el usuario autenticado (multi-tenant)
        $query = Transaction::query()
            ->where('transactions.user_id', $userId)
            ->join('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->select([
                'transactions.id',
                'transactions.date',
                'transactions.type',
                'transactions.amount',
                'transactions.currency',
                'transactions.merchant',
                'transactions.description',
                'accounts.name as account_name',
                'categories.name as category_name',
            ]);

        // Total sin filtrar (para recordsTotal)
        $totalRecords = (clone $query)->count();

        // Búsqueda global — busca en merchant, description, account y category
        $search = $request->input('search.value');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transactions.merchant', 'LIKE', "%{$search}%")
                  ->orWhere('transactions.description', 'LIKE', "%{$search}%")
                  ->orWhere('accounts.name', 'LIKE', "%{$search}%")
                  ->orWhere('categories.name', 'LIKE', "%{$search}%");
            });
        }

        // Total después de filtrar (para recordsFiltered)
        $filteredRecords = (clone $query)->count();

        // Ordenación — usamos la whitelist de columnas, nunca el valor raw
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDir         = $request->input('order.0.dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $orderColumn      = $columns[$orderColumnIndex] ?? 'transactions.date';
        $query->orderBy($orderColumn, $orderDir);

        // Paginación
        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 20);
        $transactions = $query->offset($start)->limit($length)->get();

        // Formateamos las filas para que DataTables las renderice
        $data = $transactions->map(fn ($t) => $this->formatRow($t));

        return [
            'draw'            => (int) $request->input('draw'),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data,
        ];
    }

    /**
     * Crea una nueva transacción y actualiza el saldo de la cuenta.
     * Se ejecuta dentro de una transacción DB para garantizar consistencia:
     * si algo falla, ningún cambio se guarda.
     */
    public function store(array $data, int $userId): Transaction
    {
        return DB::transaction(function () use ($data, $userId) {
            $transaction = Transaction::create([
                'user_id'     => $userId,
                'account_id'  => $data['account_id'],
                'category_id' => $data['category_id'] ?? null,
                'type'        => $data['type'],
                'amount'      => $data['amount'],
                'currency'    => $data['currency'] ?? 'EUR',
                'date'        => $data['date'],
                'merchant'    => $data['merchant'] ?? null,
                'description' => $data['description'] ?? null,
            ]);

            // Actualizamos el saldo de la cuenta
            $this->updateAccountBalance($transaction->account_id);

            return $transaction;
        });
    }

    /**
     * Actualiza una transacción existente y recalcula el saldo.
     */
    public function update(Transaction $transaction, array $data): Transaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            $oldAccountId = $transaction->account_id;

            $transaction->update([
                'account_id'  => $data['account_id'],
                'category_id' => $data['category_id'] ?? null,
                'type'        => $data['type'],
                'amount'      => $data['amount'],
                'currency'    => $data['currency'] ?? 'EUR',
                'date'        => $data['date'],
                'merchant'    => $data['merchant'] ?? null,
                'description' => $data['description'] ?? null,
            ]);

            // Si cambió de cuenta, actualizamos las dos
            $this->updateAccountBalance($oldAccountId);
            if ($oldAccountId !== (int) $data['account_id']) {
                $this->updateAccountBalance($data['account_id']);
            }

            return $transaction->fresh();
        });
    }

    /**
     * Elimina una transacción y recalcula el saldo de la cuenta.
     */
    public function destroy(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $accountId = $transaction->account_id;
            $transaction->delete();
            $this->updateAccountBalance($accountId);
        });
    }

    /**
     * Recalcula y actualiza el current_balance de una cuenta.
     *
     * current_balance = initial_balance + SUM(ingresos) - SUM(gastos)
     *
     * Se llama siempre que se crea, edita o elimina una transacción
     * para mantener el caché del saldo siempre actualizado.
     */
    private function updateAccountBalance(int $accountId): void
    {
        $account = Account::findOrFail($accountId);

        $income  = Transaction::where('account_id', $accountId)
            ->where('type', 'income')
            ->sum('amount');

        $expense = Transaction::where('account_id', $accountId)
            ->where('type', 'expense')
            ->sum('amount');

        $account->update([
            'current_balance' => $account->initial_balance + $income - $expense,
        ]);
    }

    /**
     * Formatea una fila de transacción para DataTables.
     * Centralizar el formato aquí evita lógica en las vistas.
     */
    private function formatRow(Transaction $transaction): array
    {
        return [
            'id'            => $transaction->id,
            'date'          => $transaction->date->format('d/m/Y'),
            'account_name'  => $transaction->account_name,
            'category_name' => $transaction->category_name ?? '—',
            'merchant'      => $transaction->merchant ?? '—',
            'amount'        => number_format($transaction->amount, 2, ',', '.') . ' ' . $transaction->currency,
            'type'          => $transaction->type,
            'description'   => $transaction->description ?? '',
            // Badge de tipo para renderizar en la tabla
            'type_badge'    => $this->typeBadge($transaction->type),
            // Botones de acción — el HTML se genera aquí, no en la vista
            'actions'       => $this->actionButtons($transaction->id),
        ];
    }

    /**
     * Genera el badge HTML según el tipo de transacción.
     */
    private function typeBadge(string $type): string
    {
        return match ($type) {
            'income'   => '<span class="badge badge-success">Ingreso</span>',
            'expense'  => '<span class="badge badge-danger">Gasto</span>',
            'transfer' => '<span class="badge badge-info">Transferencia</span>',
            default    => '<span class="badge badge-secondary">' . $type . '</span>',
        };
    }

    /**
     * Genera los botones de acción para cada fila de DataTables.
     */
    private function actionButtons(int $id): string
    {
        return '
            <div class="btn-group btn-group-sm">
                <button class="btn btn-info btn-edit" data-id="' . $id . '" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-delete" data-id="' . $id . '" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        ';
    }
}