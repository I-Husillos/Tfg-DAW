<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Services\DataTables\BudgetQueryService;
use Illuminate\Http\Request;

class BudgetDataController extends Controller
{
    public function __construct(
        private BudgetQueryService $queryService
    ) {}

    public function index(Request $request)
    {
        $query    = $this->queryService->buildQuery($request);
        $total    = $this->queryService->totalCount();
        $filtered = $query->count();

        $budgets = $query
            ->skip($request->input('start', 0))
            ->take($request->input('length', 15))
            ->get();

        $data = $budgets->map(fn($b) => $this->transform($b));

        return response()->json([
            'draw'            => (int) $request->input('draw'),
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data,
        ]);
    }

    private function transform(Budget $b): array
    {
        $spent      = $b->spentAmount();
        $percentage = round($b->spentPercentage() * 100, 1);
        $color      = $percentage >= 100 ? 'danger'
                    : ($percentage >= ($b->alert_threshold * 100) ? 'warning'
                    : 'success');

        return [
            'category'    => $b->category->display_name ?? $b->category->name,
            'period'      => str_pad($b->period_month, 2, '0', STR_PAD_LEFT)
                             . '/' . $b->period_year,
            'spent'       => number_format($spent, 2, ',', '.'),
            'limit'       => number_format($b->limit_amount, 2, ',', '.'),
            'percentage'  => $percentage,
            'color'       => $color,
            'threshold'   => number_format($b->alert_threshold * 100) . '%',
            'actions'     => $this->actions($b),
        ];
    }

    private function actions(Budget $b): string
    {
        $editUrl    = route('budgets.edit', $b);
        $destroyUrl = route('budgets.destroy', $b);
        $csrf       = csrf_token();
        $editLabel = __('app.edit');
        $deleteLabel = __('app.action_delete');
        $confirmDelete = __('app.confirm_delete_budget');

        return <<<HTML
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