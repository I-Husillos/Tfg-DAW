<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\DataTables\TransactionQueryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TransactionDataController extends Controller
{
    public function __construct(
        private TransactionQueryService $queryService
    ) {}

    // Endpoint para DataTables serverSide.
    // Devuelve JSON con la estructura que DataTables espera:
    // draw, recordsTotal, recordsFiltered, data.
    public function index(Request $request)
    {
        $query    = $this->queryService->buildQuery($request);
        $total    = $this->queryService->totalCount();
        $filtered = $query->count();

        $transactions = $query
            ->skip($request->input('start', 0))
            ->take($request->input('length', 15))
            ->get();

        $data = $transactions->map(fn($t) => $this->transform($t));

        return response()->json([
            'draw'            => (int) $request->input('draw'),
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data,
        ]);
    }

    // Transforma cada modelo en el array que DataTables
    // espera recibir. Cada clave coincide con el campo
    // 'data' definido en la configuración de columnas del JS.
    private function transform(Transaction $t): array
    {
        return [
            'date'        => $t->date->format('d/m/Y'),
            'name'        => $t->name ?? $t->merchant ?? '—',
            'description' => $t->description
                ?   Str::limit($t->description, 60)
                : '—',
            'category'    => $t->category
                ? ($t->category->display_name ?? $t->category->name)
                : '—',
            'type'        => $t->type === 'income' ? __('app.income') : __('app.expense'),
            'type_raw'    => $t->type,
            'amount'      => number_format($t->amount, 2, ',', '.'),
            'currency'    => $t->currency,
            'actions'     => $this->actions($t),
        ];
    }

    // Genera el HTML de los botones de acción.
    // Se hace en PHP para que las URLs tengan el
    // CSRF token correcto y sean seguras.
    private function actions(Transaction $t): string
    {
        $showUrl    = route('transactions.show', $t);
        $editUrl    = route('transactions.edit', $t);
        $destroyUrl = route('transactions.destroy', $t);
        $csrf       = csrf_token();
        $viewLabel  = __('app.action_view');
        $editLabel  = __('app.edit');
        $deleteLabel = __('app.action_delete');
        $confirmDelete = __('app.confirm_delete_transaction');

        return <<<HTML
            <a href="{$showUrl}" class="btn btn-xs btn-info" title="{$viewLabel}">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{$editUrl}" class="btn btn-xs btn-warning" title="{$editLabel}">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{$destroyUrl}" method="POST" class="d-inline"
                  onsubmit="return confirm('{$confirmDelete}')">
                <input type="hidden" name="_token" value="{$csrf}">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="btn btn-xs btn-danger" title="{$deleteLabel}">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        HTML;
    }
}