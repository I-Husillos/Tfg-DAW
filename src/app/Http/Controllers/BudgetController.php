<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Models\Budget;
use App\Models\Category;
use App\Services\BudgetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function __construct(
        private BudgetService $budgetService
    ) {}

    public function index(Request $request)
    {
        // Los presupuestos los carga DataTables vía API.
        // La vista no necesita datos calculados en PHP.
        return view('budgets.index');
    }

    public function create()
    {
        // Solo categorías de tipo expense: los presupuestos
        // son siempre de gasto, nunca de ingreso.
        $categories = Category::where('user_id', Auth::id())
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();

        return view('budgets.create', compact('categories'));
    }

    public function store(StoreBudgetRequest $request)
    {
        $this->budgetService->store($request->validated());

        return redirect()
            ->route('budgets.index')
            ->with('success', __('app.budget_stored'));
    }

    public function edit(Budget $budget)
    {
        abort_if($budget->user_id !== Auth::id(), 403);

        $categories = Category::where('user_id', Auth::id())
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();

        return view('budgets.edit', compact('budget', 'categories'));
    }

    public function update(UpdateBudgetRequest $request, Budget $budget)
    {
        $this->budgetService->update($budget, $request->validated());

        return redirect()
            ->route('budgets.index')
            ->with('success', __('app.budget_updated'));
    }

    public function destroy(Budget $budget)
    {
        abort_if($budget->user_id !== Auth::id(), 403);

        $this->budgetService->destroy($budget);

        return redirect()
            ->route('budgets.index')
            ->with('success', __('app.budget_deleted'));
    }
}