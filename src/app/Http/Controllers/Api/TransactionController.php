<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService
    ) {}

    public function store(StoreTransactionRequest $request)
    {
        $transaction = $this->transactionService->store($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('app.transaction_stored'),
                'data' => $transaction,
            ], 201);
        }

        return redirect()
            ->route('transactions.index')
            ->with('success', __('app.transaction_stored'));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $updatedTransaction = $this->transactionService->update($transaction, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('app.transaction_updated'),
                'data' => $updatedTransaction,
            ]);
        }

        return redirect()
            ->route('transactions.index')
            ->with('success', __('app.transaction_updated'));
    }

    public function destroy(Transaction $transaction, \Illuminate\Http\Request $request)
    {
        abort_if($transaction->user_id !== Auth::id(), 403);

        $this->transactionService->destroy($transaction);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('app.transaction_deleted'),
            ]);
        }

        return redirect()
            ->route('transactions.index')
            ->with('success', __('app.transaction_deleted'));
    }
}
