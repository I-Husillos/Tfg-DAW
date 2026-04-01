<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Budget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    protected string $ollamaUrl;
    protected string $ollamaModel;

    public function __construct()
    {
        $this->ollamaUrl   = env('OLLAMA_URL', 'http://service-ollama:11434');
        $this->ollamaModel = env('OLLAMA_MODEL', 'phi3:mini');
    }

    public function ask(string $question, array $history = []): ?string
    {
        if (!Auth::user()) {
            return null;
        }

        $context  = $this->buildFinancialContext();
        $prompt   = $this->buildPrompt($context, $question, $history);
        $response = $this->callOllama($prompt);

        if (!$response) {
            return null;
        }

        return $response;
    }

    private function buildFinancialContext(): array
    {
        $userId = Auth::id();

        $startDate    = now()->subDays(30);
        $transactions = Transaction::where('user_id', $userId)
            ->where('date', '>=', $startDate)
            ->with('category')
            ->orderBy('date', 'desc')
            ->limit(50)
            ->get();

        $expensesByCategory = $transactions
            ->where('type', 'expense')
            ->groupBy(fn($t) => $t->category?->name ?? 'Sin categoría')
            ->map(fn($group) => $group->sum('amount'))
            ->sortDesc()
            ->take(5)
            ->toArray();

        $currentMonth = now()->month;
        $currentYear  = now()->year;

        $totalIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->sum('amount');

        $totalExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->sum('amount');

        $budgets = Budget::where('user_id', $userId)
            ->where('period_year', $currentYear)
            ->where('period_month', $currentMonth)
            ->with('category')
            ->get()
            ->map(fn($b) => [
                'category'   => $b->category->name,
                'limit'      => $b->limit_amount,
                'spent'      => $b->spentAmount(),
                'percentage' => round($b->spentPercentage() * 100, 1),
            ])
            ->toArray();

        return [
            'total_income'        => $totalIncome,
            'total_expense'       => $totalExpense,
            'balance'             => $totalIncome - $totalExpense,
            'expenses_top'        => $expensesByCategory,
            'budgets'             => $budgets,
            'recent_transactions' => $transactions->take(10)->map(fn($t) => [
                'date'     => $t->date->format('d/m/Y'),
                'amount'   => $t->amount,
                'type'     => $t->type,
                'category' => $t->category?->name ?? 'Sin categoría',
                'name'     => $t->name ?? $t->merchant ?? '',
            ])->toArray(),
        ];
    }

    private function buildPrompt(array $context, string $question, array $history = []): string
    {
        $text  = "Eres un asistente financiero personal.\n";
        $text .= "Responde solo con los datos proporcionados.\n";
        $text .= "Si no hay información suficiente, dilo claramente.\n";
        $text .= "Responde en español de forma clara y breve.\n\n";

        $text .= "DATOS DEL USUARIO\n";
        $text .= "- Ingresos este mes: {$context['total_income']} €\n";
        $text .= "- Gastos este mes: {$context['total_expense']} €\n";
        $text .= "- Balance: {$context['balance']} €\n\n";

        $text .= "TOP GASTOS POR CATEGORÍA\n";
        foreach ($context['expenses_top'] as $cat => $amount) {
            $text .= "- {$cat}: {$amount} €\n";
        }

        if (!empty($context['budgets'])) {
            $text .= "\nPRESUPUESTOS\n";
            foreach ($context['budgets'] as $budget) {
                $text .= "- {$budget['category']}: {$budget['spent']} € de {$budget['limit']} € ({$budget['percentage']}%)\n";
            }
        }

        if (!empty($context['recent_transactions'])) {
            $text .= "\nÚLTIMAS TRANSACCIONES\n";
            foreach ($context['recent_transactions'] as $t) {
                $sign  = $t['type'] === 'income' ? '+' : '-';
                $text .= "- {$t['date']}: {$t['name']} {$sign}{$t['amount']} € ({$t['category']})\n";
            }
        }

        if (!empty($history)) {
            $text .= "\nHISTORIAL DE CONVERSACIÓN\n";
            foreach ($history as $item) {
                $prefix = $item['role'] === 'user' ? 'Usuario' : 'Asistente';
                $text  .= "{$prefix}: {$item['content']}\n";
            }
        }

        $text .= "\nPREGUNTA ACTUAL\n{$question}\n";

        return $text;
    }

    private function callOllama(string $prompt): ?string
    {
        try {
            $response = Http::timeout(60)->post("{$this->ollamaUrl}/api/generate", [
                'model'  => $this->ollamaModel,
                'prompt' => $prompt,
                'stream' => false,
            ]);

            if ($response->failed()) {
                Log::error('Ollama error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            return $response->json('response');
        } catch (\Exception $e) {
            Log::error('Ollama exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}