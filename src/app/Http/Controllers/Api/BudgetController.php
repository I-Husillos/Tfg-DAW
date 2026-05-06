<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Models\Budget;
use App\Services\BudgetService;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function __construct(
        private BudgetService $budgetService
    ) {}

    public function store(StoreBudgetRequest $request)
    {
        $budget = $this->budgetService->store($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('app.budget_stored'),
                'data' => $budget,
            ], 201);
        }

        return redirect()
            ->route('budgets.index')
            ->with('success', __('app.budget_stored'));
    }

    public function update(UpdateBudgetRequest $request, Budget $budget)
    {
        $updatedBudget = $this->budgetService->update($budget, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('app.budget_updated'),
                'data' => $updatedBudget,
            ]);
        }

        return redirect()
            ->route('budgets.index')
            ->with('success', __('app.budget_updated'));
    }

    public function destroy(Budget $budget, \Illuminate\Http\Request $request)
    {
        abort_if($budget->user_id !== Auth::id(), 403);

        $this->budgetService->destroy($budget);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('app.budget_deleted'),
            ]);
        }

        return redirect()
            ->route('budgets.index')
            ->with('success', __('app.budget_deleted'));
    }
}
