<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Budget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    private const DEFAULT_RECENT_DAYS = 30;
    private const DEFAULT_RECENT_LIMIT = 50;
    private const DEFAULT_PROMPT_TRANSACTIONS_LIMIT = 10;
    private const DEFAULT_HISTORY_LIST_LIMIT = 200;
    private const DEFAULT_HISTORY_MONTHS_SUMMARY = 24;

    protected string $ollamaUrl;
    protected string $ollamaModel;

    public function __construct()
    {
        $this->ollamaUrl   = env('OLLAMA_URL', env('OLLAMA_HOST', 'http://service-ollama:11434'));
        $this->ollamaModel = env('OLLAMA_MODEL', 'llama3.2:1b');
    }

    public function ask(string $question, array $history = []): ?string
    {
        if (!Auth::user()) {
            return null;
        }

        $context  = $this->buildFinancialContext($question);
        $messages = $this->buildMessages($context, $question, $history);
        $response = $this->callOllama($messages);

        if (!$response) {
            return null;
        }

        return $response;
    }

    private function buildFinancialContext(string $question): array
    {
        $userId = Auth::id();
        $scope  = $this->resolveScope($question);
        $wantsExpenseListing = $this->asksForExpenseListing($question);

        if ($scope === 'historical' || $wantsExpenseListing) {
            return $this->buildHistoricalContext($userId, $wantsExpenseListing);
        }

        return $this->buildRecentContext($userId);
    }

    private function buildRecentContext(int $userId): array
    {
        $recentDays  = (int) env('AI_RECENT_DAYS', self::DEFAULT_RECENT_DAYS);
        $recentLimit = (int) env('AI_RECENT_LIMIT', self::DEFAULT_RECENT_LIMIT);

        $startDate    = now()->subDays(max($recentDays, 1));
        $transactions = Transaction::where('user_id', $userId)
            ->where('date', '>=', $startDate)
            ->with('category')
            ->orderBy('date', 'desc')
            ->limit(max($recentLimit, 1))
            ->get();

        $expensesByCategory = $transactions
            ->where('type', 'expense')
            ->groupBy(fn($t) => $t->category?->name ?? __('app.ai_no_category'))
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

        $promptTransactionsLimit = (int) env('AI_PROMPT_TRANSACTIONS_LIMIT', self::DEFAULT_PROMPT_TRANSACTIONS_LIMIT);

        return [
            'scope'               => 'recent',
            'total_income'        => $totalIncome,
            'total_expense'       => $totalExpense,
            'balance'             => $totalIncome - $totalExpense,
            'expenses_top'        => $expensesByCategory,
            'budgets'             => $budgets,
            'recent_transactions' => $transactions->take(max($promptTransactionsLimit, 1))->map(fn($t) => [
                'date'     => $t->date->format('d/m/Y'),
                'amount'   => $t->amount,
                'type'     => $t->type,
                'category' => $t->category?->name ?? __('app.ai_no_category'),
                'name'     => $t->name ?? $t->merchant ?? '',
            ])->toArray(),
            'period_label'        => 'Mes actual',
        ];
    }

    private function buildHistoricalContext(int $userId, bool $includeExpenseListing = false): array
    {
        $monthsSummary = (int) env('AI_HISTORY_MONTHS_SUMMARY', self::DEFAULT_HISTORY_MONTHS_SUMMARY);
        $monthsSummary = max($monthsSummary, 1);
        $listLimit = max((int) env('AI_HISTORY_LIST_LIMIT', self::DEFAULT_HISTORY_LIST_LIMIT), 50);

        $cacheKey = sprintf(
            'ai:historical_context:%d:%d:%d',
            $userId,
            $monthsSummary,
            $includeExpenseListing ? 1 : 0
        );

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($userId, $monthsSummary, $includeExpenseListing, $listLimit) {
            $totalIncome = Transaction::where('user_id', $userId)
                ->where('type', 'income')
                ->sum('amount');

            $totalExpense = Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->sum('amount');

            $expensesByCategory = Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->with('category')
                ->orderBy('date', 'desc')
                ->limit(1000)
                ->get()
                ->groupBy(fn($t) => $t->category?->name ?? __('app.ai_no_category'))
                ->map(fn($group) => $group->sum('amount'))
                ->sortDesc()
                ->take(8)
                ->toArray();

            $recentTransactionsQuery = Transaction::where('user_id', $userId)
                ->with('category')
                ->orderBy('date', 'desc');

            if ($includeExpenseListing) {
                $recentTransactionsQuery->where('type', 'expense');
            }

            $recentTransactions = $recentTransactionsQuery
                ->limit($includeExpenseListing ? $listLimit : 20)
                ->get()
                ->map(fn($t) => [
                    'date'     => $t->date->format('d/m/Y'),
                    'amount'   => $t->amount,
                    'type'     => $t->type,
                    'category' => $t->category?->name ?? __('app.ai_no_category'),
                    'name'     => $t->name ?? $t->merchant ?? '',
                ])
                ->toArray();

            $monthlySummary = Transaction::where('user_id', $userId)
                ->selectRaw('YEAR(date) as year, MONTH(date) as month')
                ->selectRaw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income")
                ->selectRaw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense")
                ->groupByRaw('YEAR(date), MONTH(date)')
                ->orderByRaw('YEAR(date) DESC, MONTH(date) DESC')
                ->limit($monthsSummary)
                ->get()
                ->map(fn($row) => [
                    'period'  => sprintf('%02d/%04d', (int) $row->month, (int) $row->year),
                    'income'  => (float) $row->income,
                    'expense' => (float) $row->expense,
                    'balance' => (float) $row->income - (float) $row->expense,
                ])
                ->toArray();

            return [
                'scope'               => 'historical',
                'total_income'        => $totalIncome,
                'total_expense'       => $totalExpense,
                'balance'             => $totalIncome - $totalExpense,
                'expenses_top'        => $expensesByCategory,
                'budgets'             => [],
                'recent_transactions' => $recentTransactions,
                'monthly_summary'     => $monthlySummary,
                'period_label'        => 'Historico completo',
                'requested_listing'   => $includeExpenseListing,
            ];
        });
    }

    private function resolveScope(string $question): string
    {
        $normalized = mb_strtolower($question);

        $historicalPattern = '/(todos|todas|hist[oó]rico|anteriores|desde.*inicio|desde siempre|todo el tiempo|a[ñn]os?|meses? anteriores)/u';

        return preg_match($historicalPattern, $normalized) ? 'historical' : 'recent';
    }

    private function asksForExpenseListing(string $question): bool
    {
        $normalized = mb_strtolower($question);

        $listingPattern = '/(dime.*todos.*gastos|todos.*gastos.*recientes.*antiguos|lista.*gastos|listado.*gastos|enumera.*gastos|mostrar.*gastos.*orden)/u';

        return (bool) preg_match($listingPattern, $normalized);
    }

    private function buildMessages(array $context, string $question, array $history = []): array
    {
        $systemRules = [
            'Eres un asistente financiero de SmartBudget.',
            'Responde de forma natural, directa y util para la pregunta del usuario.',
            'No uses plantillas rigidas ni repitas bloques de datos si no lo piden.',
            'Si faltan datos para responder con precision, dilo claramente.',
            'No inventes transacciones, importes o fechas fuera del contexto recibido.',
            'Cuando el usuario pida listados, devuelve una lista ordenada y legible.',
            'Idioma de respuesta: espanol.',
        ];

        $messages = [
            [
                'role' => 'system',
                'content' => implode("\n", $systemRules),
            ],
            [
                'role' => 'system',
                'content' => 'Contexto financiero disponible (JSON): ' . json_encode($context, JSON_UNESCAPED_UNICODE),
            ],
        ];

        if (!empty($history)) {
            foreach ($history as $item) {
                if (!isset($item['role'], $item['content'])) {
                    continue;
                }

                $role = $item['role'] === 'assistant' ? 'assistant' : 'user';
                $messages[] = [
                    'role' => $role,
                    'content' => (string) $item['content'],
                ];
            }
        }

        $messages[] = [
            'role' => 'user',
            'content' => $question,
        ];

        return $messages;
    }

    private function callOllama(array $messages): ?string
    {
        try {
            $response = Http::timeout(60)->post("{$this->ollamaUrl}/api/chat", [
                'model'  => $this->ollamaModel,
                'messages' => $messages,
                'stream' => false,
            ]);

            if ($response->failed()) {
                Log::error('Ollama error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            return $response->json('message.content');
        } catch (\Exception $e) {
            Log::error('Ollama exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}