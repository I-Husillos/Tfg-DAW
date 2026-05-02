@extends('layouts.app')

@section('title', __('app.budgets'))

@push('breadcrumb')
    <li class="breadcrumb-item active">{{ __('app.budgets') }}</li>
@endpush

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-wallet mr-1"></i> {{ __('app.budgets') }}
        </h3>
        <div class="card-tools">
            <a href="{{ route('budgets.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-1"></i> {{ __('app.new_budget') }}
            </a>
        </div>
    </div>
    <div class="card-body">

        <div class="row mb-3">
            <div class="col-md-2 col-sm-6 mb-2">
                <select id="filter-year" class="form-control">
                    @foreach(range(2020, now()->year + 1) as $y)
                        <option value="{{ $y }}"
                            {{ $y == now()->year ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-sm-6 mb-2">
                <select id="filter-month" class="form-control">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}"
                            {{ $m == now()->month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-sm-6 mb-2">
                <button id="clear-filters" class="btn btn-secondary btn-block">
                    <i class="fas fa-times mr-1"></i> {{ __('app.filter_clear_filters') }}
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="tabla-presupuestos"
                   class="table table-hover table-striped table-bordered mb-0 text-center dt-responsive"
                   data-api-url="{{ route('api.budgets.index') }}">
                <thead class="text-center bg-white font-weight-bold">
                    <tr>
                        <th>{{ __('app.col_category') }}</th>
                        <th>{{ __('app.col_period') }}</th>
                        <th>{{ __('app.col_spent') }}</th>
                        <th>{{ __('app.col_limit') }}</th>
                        <th>{{ __('app.col_progress') }}</th>
                        <th>{{ __('app.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>
</div>

@endsection