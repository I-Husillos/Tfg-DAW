<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    // El servicio se inyecta en el constructor.
    // El controlador nunca instancia el servicio
    // directamente: Laravel lo resuelve por el
    // contenedor de dependencias (IoC container).
    public function __construct(
        private TransactionService $transactionService
    ) {}

    public function index(Request $request)
    {
        // Las transacciones las carga DataTables vía API.
        // Solo pasamos las categorías para poblar el filtro.
        $categories = Category::where('user_id', Auth::id())
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('transactions.index', compact('categories'));
    }

    // Muestra el formulario de creación.
    public function create()
    {
        // Solo se pasan las categorías del usuario
        // para poblar el selector del formulario.
        $categories = Category::where('user_id', Auth::id())
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('transactions.create', compact('categories'));
    }

    // Procesa el formulario de creación.
    // La validación ya la hizo StoreTransactionRequest
    // antes de llegar aquí. El controlador solo delega
    // al servicio y redirige.
    public function store(StoreTransactionRequest $request)
    {
        $this->transactionService->store($request->validated());

        return redirect()
            ->route('transactions.index')
            ->with('success', __('app.transaction_stored'));
    }

    // Muestra el detalle de una transacción.
    // Route model binding resuelve Transaction
    // automáticamente desde el {transaction} de la URL.
    public function show(Transaction $transaction)
    {
        // Verificamos que la transacción pertenece
        // al usuario autenticado. Si no, 403.
        abort_if($transaction->user_id !== Auth::id(), 403);

        $transaction->load('category');

        return view('transactions.show', compact('transaction'));
    }

    // Muestra el formulario de edición.
    public function edit(Transaction $transaction)
    {
        abort_if($transaction->user_id !== Auth::id(), 403);

        $categories = Category::where('user_id', Auth::id())
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('transactions.edit', compact('transaction', 'categories'));
    }

    // Procesa el formulario de edición.
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $this->transactionService->update($transaction, $request->validated());

        return redirect()
            ->route('transactions.index')
            ->with('success', __('app.transaction_updated'));
    }

    // Elimina una transacción con confirmación previa
    // mediante el método DELETE desde la vista.
    public function destroy(Transaction $transaction)
    {
        abort_if($transaction->user_id !== Auth::id(), 403);

        $this->transactionService->destroy($transaction);

        return redirect()
            ->route('transactions.index')
            ->with('success', __('app.transaction_deleted'));
    }
}