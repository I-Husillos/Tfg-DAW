@extends('layouts.app')

@section('title', __('app.reports'))

@push('breadcrumb')
<li class="breadcrumb-item active">{{ __('app.reports') }}</li>
@endpush

@section('content')

{{-- Selector de período --}}
<div class="card">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.index') }}" class="form-inline">
            <div class="form-group mr-3">
                <label class="mr-2">{{ __('app.label_year') }}</label>
                <select name="year" class="form-control">
                    @foreach($years as $y)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mr-3">
                <label class="mr-2">{{ __('app.label_month') }}</label>
                <select name="month" class="form-control">
                    @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary mr-2">
                <i class="fas fa-search mr-1"></i> {{ __('app.view_report') }}
            </button>
            <a href="{{ route('reports.export.pdf', ['year' => $year, 'month' => $month]) }}"
                class="btn btn-danger">
                <i class="fas fa-file-pdf mr-1"></i> {{ __('app.export_pdf') }}
            </a>
        </form>
    </div>
</div>

{{-- Tarjetas resumen --}}
<div class="row">
    <div class="col-lg-4">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ number_format($totalIncome, 2, ',', '.') }} €</h3>
                <p>{{ __('app.total_income') }}</p>
            </div>
            <div class="icon"><i class="fas fa-arrow-up"></i></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ number_format($totalExpense, 2, ',', '.') }} €</h3>
                <p>{{ __('app.total_expense') }}</p>
            </div>
            <div class="icon"><i class="fas fa-arrow-down"></i></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="small-box {{ $balance >= 0 ? 'bg-info' : 'bg-warning' }}">
            <div class="inner">
                <h3>{{ number_format($balance, 2, ',', '.') }} €</h3>
                <p>{{ __('app.balance') }}</p>
            </div>
            <div class="icon"><i class="fas fa-balance-scale"></i></div>
        </div>
    </div>
</div>

<div class="row">

    {{-- Gráfico de evolución diaria --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-1"></i>
                    {{ __('app.daily_evolution') }} —
                    {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}
                    {{ $year }}
                </h3>
            </div>
            <div class="card-body">
                <canvas id="chartDaily"
                        height="120"
                        data-labels="{{ json_encode($dailyData['labels']) }}"
                        data-income="{{ json_encode($dailyData['income']) }}"
                        data-expense="{{ json_encode($dailyData['expense']) }}">
                </canvas>
            </div>
        </div>
    </div>

    {{-- Gráfico de gastos por categoría --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>
                    {{ __('app.expense_by_category') }}
                </h3>
            </div>
            <div class="card-body">
                @if($expensesByCategory->isEmpty())
                <p class="text-muted text-center py-3">
                    {{ __('app.no_expenses_period') }}
                </p>
                @else
                <canvas id="chartCategories"
                        height="220"
                    data-labels='@json($expensesByCategory->keys()->values())'
                    data-amounts='@json($expensesByCategory->values())'>
                </canvas>
                @endif
            </div>
        </div>
    </div>

</div>

<div class="row">

    {{-- Desglose por categoría --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tags mr-1"></i> {{ __('app.breakdown_by_category') }}
                </h3>
            </div>
            <div class="card-body p-0">
                @if($expensesByCategory->isEmpty())
                <p class="text-muted text-center py-4">{{ __('app.no_data') }}</p>
                @else
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('app.col_category') }}</th>
                            <th class="text-right">{{ __('app.col_amount') }}</th>
                            <th class="text-right">{{ __('app.percent_total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expensesByCategory as $category => $amount)
                        <tr>
                            <td>{{ $category }}</td>
                            <td class="text-right">
                                {{ number_format($amount, 2, ',', '.') }} €
                            </td>
                            <td class="text-right">
                                {{ $totalExpense > 0
                                               ? number_format(($amount / $totalExpense) * 100, 1)
                                               : 0 }}%
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>

    {{-- Top 5 gastos --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list-ol mr-1"></i> {{ __('app.top5_expenses') }}
                </h3>
            </div>
            <div class="card-body p-0">
                @if($topExpenses->isEmpty())
                <p class="text-muted text-center py-4">{{ __('app.no_data') }}</p>
                @else
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('app.col_concept') }}</th>
                            <th>{{ __('app.col_date') }}</th>
                            <th class="text-right">{{ __('app.col_amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topExpenses as $t)
                        <tr>
                            <td>{{ $t->name ?? $t->merchant ?? '—' }}</td>
                            <td>{{ $t->date->format('d/m') }}</td>
                            <td class="text-right text-danger font-weight-bold">
                                {{ number_format($t->amount, 2, ',', '.') }} €
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- Estado de presupuestos --}}
@if($budgets->isNotEmpty())
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-wallet mr-1"></i> {{ __('app.report_budget_status') }}
        </h3>
    </div>
    <div class="card-body">
        @foreach($budgets as $budget)
        @php
        $color = $budget->percentage >= 100 ? 'danger'
        : ($budget->percentage >= ($budget->alert_threshold * 100) ? 'warning'
        : 'success');
        @endphp
        <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
                <span>
                    {{ $budget->category->display_name ?? $budget->category->name }}
                </span>
                <span class="text-muted small">
                    {{ number_format($budget->spent, 2, ',', '.') }} €
                    / {{ number_format($budget->limit_amount, 2, ',', '.') }} €
                    ({{ $budget->percentage }}%)
                </span>
            </div>
            <div class="progress progress-sm">
                <div class="progress-bar bg-{{ $color }}"
                    style="width: {{ min($budget->percentage, 100) }}%">
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection