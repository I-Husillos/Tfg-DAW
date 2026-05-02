@extends('layouts.app')

@section('title', 'Dashboard')

@push('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endpush

@section('content')
@if(isset($budgetAlerts) && $budgetAlerts->count() > 0)
<div class="row mb-3">
    <div class="col-12">
        @foreach($budgetAlerts as $alert)
        <div class="alert alert-{{ $alert['exceeded'] ? 'danger' : 'warning' }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
            <h5>
                <i class="fas fa-exclamation-triangle mr-1"></i>
                @if($alert['exceeded'])
                {{ __('app.budget_exceeded') }} <strong>{{ $alert['category'] }}</strong>
                @else
                {{ __('app.budget_alert_label') }} <strong>{{ $alert['category'] }}</strong>
                @endif
            </h5>
            {{ __('app.spent_of_limit', ['amount' => number_format($alert['spent'], 2, ',', '.') . ' ' . user_currency(), 'pct' => $alert['percentage']]) }}
            <a href="{{ route('budgets.index') }}" class="alert-link ml-1">
                {{ __('app.view_budgets') }}
            </a>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Tarjetas resumen del mes --}}
<div class="row">

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ number_format($totalIncome, 2, ',', '.') }} {{ user_currency() }}</h3>
                <p>{{ __('app.income_this_month') }}</p>
            </div>
            <div class="icon"><i class="fas fa-arrow-up"></i></div>
            <a href="{{ route('transactions.index', ['type' => 'income']) }}"
                class="small-box-footer">
                {{ __('app.view_detail') }} <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ number_format($totalExpense, 2, ',', '.') }} {{ user_currency() }}</h3>
                <p>{{ __('app.expense_this_month') }}</p>
            </div>
            <div class="icon"><i class="fas fa-arrow-down"></i></div>
            <a href="{{ route('transactions.index', ['type' => 'expense']) }}"
                class="small-box-footer">
                {{ __('app.view_detail') }} <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box {{ $balance >= 0 ? 'bg-info' : 'bg-warning' }}">
            <div class="inner">
                <h3>{{ number_format($balance, 2, ',', '.') }} {{ user_currency() }}</h3>
                <p>{{ __('app.balance_this_month') }}</p>
            </div>
            <div class="icon"><i class="fas fa-balance-scale"></i></div>
            <a href="{{ route('reports.index') }}" class="small-box-footer">
                {{ __('app.view_reports') }} <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ $transactionCount }}</h3>
                <p>{{ __('app.transactions_this_month') }}</p>
            </div>
            <div class="icon"><i class="fas fa-list-ul"></i></div>
            <a href="{{ route('transactions.index') }}" class="small-box-footer">
                {{ __('app.view_all') }} <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

</div>

<div class="row">

    {{-- Últimas transacciones --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exchange-alt mr-1"></i>
                    {{ __('app.latest_transactions') }}
                </h3>
                <div class="card-tools">
                    <a href="{{ route('transactions.index') }}"
                        class="btn btn-sm btn-primary">
                        {{ __('app.view_all') }}
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @if($latestTransactions->isEmpty())
                <p class="text-muted text-center py-4">
                    {{ __('app.no_transactions_yet') }}
                </p>
                @else
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('app.col_date') }}</th>
                            <th>{{ __('app.col_description') }}</th>
                            <th>{{ __('app.col_category') }}</th>
                            <th class="text-right">{{ __('app.col_amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($latestTransactions as $transaction)
                        <tr>
                            <td>{{ $transaction->date->format('d/m/Y') }}</td>
                            <td>{{ $transaction->name ?? $transaction->merchant ?? '—' }}</td>
                            <td>
                                {{ $transaction->category?->display_name
                                               ?? $transaction->category?->name
                                               ?? '—' }}
                            </td>
                            <td class="text-right">
                                <span class="badge badge-{{ $transaction->type === 'income' ? 'success' : 'danger' }}">
                                    {{ $transaction->type === 'income' ? '+' : '-' }}
                                    {{ number_format($transaction->amount, 2, ',', '.') }} {{ user_currency() }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>

    {{-- Presupuestos del mes --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-wallet mr-1"></i>
                    {{ __('app.monthly_budgets') }}
                </h3>
                <div class="card-tools">
                    <a href="{{ route('budgets.index') }}"
                        class="btn btn-sm btn-primary">
                        {{ __('app.view_all') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($budgets->isEmpty())
                <p class="text-muted text-center py-3">
                    {{ __('app.no_budgets_this_month') }}
                </p>
                @else
                @foreach($budgets as $budget)
                @php
                $spent = $budget->spentAmount();
                $percentage = $budget->spentPercentage() * 100;
                $color = $percentage >= 100 ? 'danger'
                : ($percentage >= 80 ? 'warning' : 'success');
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>
                            {{ $budget->category->display_name
                                           ?? $budget->category->name }}
                        </span>
                        <span class="text-muted small">
                            {{ number_format($spent, 2, ',', '.') }} {{ user_currency() }}
                            / {{ number_format($budget->limit_amount, 2, ',', '.') }} {{ user_currency() }}
                        </span>
                    </div>
                    {{-- AdminLTE progress bar nativa --}}
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-{{ $color }}"
                            style="width: {{ min($percentage, 100) }}%"
                            role="progressbar"
                            aria-valuenow="{{ $percentage }}"
                            aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>

</div>

@endsection