<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

/**
 * Servicio responsable de las operaciones CRUD sobre transacciones.
 */
class TransactionService
{
    /**
     * Inyección de dependencias a través del constructor.
     *
     * Laravel resuelve automáticamente BudgetNotificationService
     * cuando el contenedor de dependencias instancia TransactionService.
     * No necesitas instanciarlo manualmente en ningún sitio.
     */
    public function __construct(
        private BudgetNotificationService $budgetNotificationService
    ) {}

    /**
     * Crea una nueva transacción y registra el evento en audit_logs.
     * Si la transacción es un gasto, comprueba si algún presupuesto
     * ha alcanzado su umbral de alerta y notifica al usuario.
     */
    public function store(array $data): Transaction
    {
        $transaction = Transaction::create([
            'user_id'     => Auth::id(),
            'category_id' => $data['category_id'] ?? null,
            'type'        => $data['type'],
            'amount'      => $data['amount'],
            'currency'    => $data['currency'] ?? 'EUR',
            'date'        => $data['date'],
            'name'        => $data['name'] ?? null,
            'merchant'    => $data['merchant'] ?? null,
            'description' => $data['description'] ?? null,
        ]);

        $this->audit('created', $transaction, []);

        // Solo comprobamos alertas si el gasto puede afectar a un presupuesto.
        // Los ingresos no consumen presupuesto.
        if ($transaction->type === 'expense') {
            $this->budgetNotificationService->checkAndNotify($transaction->user_id);
        }

        return $transaction;
    }

    /**
     * Actualiza una transacción existente y registra en audit_logs
     * exactamente qué campos cambiaron.
     * Si la transacción actualizada es un gasto, comprueba alertas.
     */
    public function update(Transaction $transaction, array $data): Transaction
    {
        $original = $transaction->only(array_keys($data));

        $transaction->update([
            'category_id' => $data['category_id'] ?? null,
            'type'        => $data['type'],
            'amount'      => $data['amount'],
            'currency'    => $data['currency'] ?? 'EUR',
            'date'        => $data['date'],
            'name'        => $data['name'] ?? null,
            'merchant'    => $data['merchant'] ?? null,
            'description' => $data['description'] ?? null,
        ]);

        $diff = [];
        foreach ($data as $key => $newValue) {
            if (isset($original[$key]) && (string) $original[$key] !== (string) $newValue) {
                $diff[$key] = [$original[$key], $newValue];
            }
        }

        $this->audit('updated', $transaction, $diff);

        // Al editar una transacción de tipo expense (ya sea que se cambió
        // el tipo o el importe), también comprobamos las alertas.
        // Nota: usamos el tipo ACTUALIZADO de la transacción.
        $transaction->refresh(); // recargamos para obtener el tipo actualizado
        if ($transaction->type === 'expense') {
            $this->budgetNotificationService->checkAndNotify($transaction->user_id);
        }

        return $transaction;
    }

    /**
     * Elimina una transacción.
     * No comprobamos alertas al eliminar porque eliminar un gasto
     * solo puede reducir el consumo del presupuesto, nunca cruzar
     * un umbral hacia arriba.
     */
    public function destroy(Transaction $transaction): void
    {
        $this->audit('deleted', $transaction, $transaction->toArray());

        $transaction->delete();
    }

    /**
     * Escribe un registro en audit_logs.
     * Método privado porque solo este servicio debe usarlo.
     */
    private function audit(string $action, Transaction $transaction, array $diff): void
    {
        AuditLog::create([
            'action'   => $action,
            'model'    => 'Transaction',
            'model_id' => $transaction->id,
            'diff'     => $diff,
        ]);
    }
}