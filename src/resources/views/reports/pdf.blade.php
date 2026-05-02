<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('app.pdf_title') }} — {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}</title>
    <style>
        /* Estilos básicos solo para el PDF.
           No usamos AdminLTE aquí porque el PDF
           se renderiza fuera del navegador. */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 4px;
        }

        h2 {
            font-size: 14px;
            margin-top: 20px;
            margin-bottom: 8px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        th {
            background: #f0f0f0;
            text-align: left;
            padding: 6px 8px;
            font-size: 11px;
        }

        td {
            padding: 5px 8px;
            border-bottom: 1px solid #eee;
        }

        .text-right {
            text-align: right;
        }

        .summary {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
        }

        .summary-box {
            flex: 1;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 4px;
        }

        .summary-box .amount {
            font-size: 18px;
            font-weight: bold;
        }

        .income {
            color: #28a745;
        }

        .expense {
            color: #dc3545;
        }

        .balance {
            color: #17a2b8;
        }

        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #999;
            text-align: center;
        }
    </style>
</head>

<body>

    <h1>{{ __('app.pdf_title') }}</h1>
    <p>
        {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}
        &nbsp;·&nbsp; {{ __('app.generated_on') }} {{ now()->format('d/m/Y H:i') }}
    </p>

    {{-- Resumen --}}
    <h2>{{ __('app.pdf_period_summary') }}</h2>
    <table>
        <tr>
            <th>{{ __('app.col_concept') }}</th>
            <th class="text-right">{{ __('app.col_amount') }}</th>
        </tr>
        <tr>
            <td>{{ __('app.pdf_total_income') }}</td>
            <td class="text-right income">
                + {{ number_format($totalIncome, 2, ',', '.') }} {{ user_currency() }}
            </td>
        </tr>
        <tr>
            <td>{{ __('app.pdf_total_expense') }}</td>
            <td class="text-right expense">
                - {{ number_format($totalExpense, 2, ',', '.') }} {{ user_currency() }}
            </td>
        </tr>
        <tr>
            <td><strong>{{ __('app.pdf_balance') }}</strong></td>
            <td class="text-right {{ $balance >= 0 ? 'income' : 'expense' }}">
                <strong>{{ number_format($balance, 2, ',', '.') }} {{ user_currency() }}</strong>
            </td>
        </tr>
    </table>

    {{-- Gastos por categoría --}}
    @if($expensesByCategory->isNotEmpty())
    <h2>{{ __('app.pdf_expense_by_category') }}</h2>
    <table>
        <tr>
            <th>{{ __('app.col_category') }}</th>
            <th class="text-right">{{ __('app.col_amount') }}</th>
            <th class="text-right">{{ __('app.percent_total') }}</th>
        </tr>
        @foreach($expensesByCategory as $category => $amount)
        <tr>
            <td>{{ $category }}</td>
            <td class="text-right">
                {{ number_format($amount, 2, ',', '.') }} {{ user_currency() }}
            </td>
            <td class="text-right">
                {{ $totalExpense > 0
                           ? number_format(($amount / $totalExpense) * 100, 1)
                           : 0 }}%
            </td>
        </tr>
        @endforeach
    </table>
    @endif

    {{-- Top gastos --}}
    @if($topExpenses->isNotEmpty())
    <h2>{{ __('app.pdf_top5') }}</h2>
    <table>
        <tr>
            <th>{{ __('app.col_concept') }}</th>
            <th>{{ __('app.col_category') }}</th>
            <th>{{ __('app.col_date') }}</th>
            <th class="text-right">{{ __('app.col_amount') }}</th>
        </tr>
        @foreach($topExpenses as $t)
        <tr>
            <td>{{ $t->name ?? $t->merchant ?? '—' }}</td>
            <td>{{ $t->category?->name ?? '—' }}</td>
            <td>{{ $t->date->format('d/m/Y') }}</td>
            <td class="text-right expense">
                {{ number_format($t->amount, 2, ',', '.') }} {{ user_currency() }}
            </td>
        </tr>
        @endforeach
    </table>
    @endif

    {{-- Presupuestos --}}
    @if($budgets->isNotEmpty())
    <h2>{{ __('app.pdf_budgets') }}</h2>
    <table>
        <tr>
            <th>{{ __('app.col_category') }}</th>
            <th class="text-right">{{ __('app.col_spent') }}</th>
            <th class="text-right">{{ __('app.col_limit') }}</th>
            <th class="text-right">%</th>
        </tr>
        @foreach($budgets as $budget)
        <tr>
            <td>
                {{ $budget->category->display_name ?? $budget->category->name }}
            </td>
            <td class="text-right">
                {{ number_format($budget->spent, 2, ',', '.') }} {{ user_currency() }}
            </td>
            <td class="text-right">
                {{ number_format($budget->limit_amount, 2, ',', '.') }} {{ user_currency() }}
            </td>
            <td class="text-right {{ $budget->percentage >= 100 ? 'expense' : ($budget->percentage >= 80 ? '' : 'income') }}">
                {{ $budget->percentage }}%
            </td>
        </tr>
        @endforeach
    </table>
    @endif

    {{-- Todas las transacciones --}}
    @if($transactions->isNotEmpty())
    <h2>{{ __('app.transactions_detail') }}</h2>
    <table>
        <tr>
            <th>{{ __('app.col_date') }}</th>
            <th>{{ __('app.col_concept') }}</th>
            <th>{{ __('app.col_category') }}</th>
            <th>{{ __('app.col_type') }}</th>
            <th class="text-right">{{ __('app.col_amount') }}</th>
        </tr>
        @foreach($transactions as $t)
        <tr>
            <td>{{ $t->date->format('d/m/Y') }}</td>
            <td>{{ $t->name ?? $t->merchant ?? '—' }}</td>
            <td>{{ $t->category?->name ?? '—' }}</td>
            <td>{{ $t->type === 'income' ? __('app.income') : __('app.expense') }}</td>
            <td class="text-right {{ $t->type === 'income' ? 'income' : 'expense' }}">
                {{ $t->type === 'income' ? '+' : '-' }}
                {{ number_format($t->amount, 2, ',', '.') }} {{ user_currency() }}
            </td>
        </tr>
        @endforeach
    </table>
    @endif

    <div class="footer">
        SmartBudget · {{ __('app.report_generated_auto') }} · {{ now()->format('d/m/Y H:i') }}
    </div>

</body>

</html>