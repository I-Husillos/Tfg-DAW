@extends('layouts.app')

@section('title', __('app.new_budget'))

@push('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('budgets.index') }}">{{ __('app.budgets') }}</a>
</li>
<li class="breadcrumb-item active">{{ __('app.new_budget') }}</li>
@endpush

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-plus mr-1"></i> {{ __('app.new_budget') }}
        </h3>
    </div>
    <form action="{{ route('budgets.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category_id">
                            {{ __('app.label_category') }} <span class="text-danger">*</span>
                        </label>
                        <select name="category_id" id="category_id"
                            data-category-select
                            data-placeholder="{{ __('app.search_category') }}"
                            class="form-control @error('category_id') is-invalid @enderror">
                            <option value="">{{ __('app.select_expense_category') }}</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->display_name ?? $category->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="limit_amount">
                            {{ __('app.label_limit_amount') }} <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" name="limit_amount" id="limit_amount"
                                step="0.01" min="0.01"
                                class="form-control @error('limit_amount') is-invalid @enderror"
                                value="{{ old('limit_amount') }}"
                                placeholder="0,00">
                            <div class="input-group-append">
                                <span class="input-group-text">{{ user_currency() }}</span>
                            </div>
                            @error('limit_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="period_year">
                            {{ __('app.label_year') }} <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="period_year" id="period_year"
                            class="form-control @error('period_year') is-invalid @enderror"
                            value="{{ old('period_year', now()->year) }}"
                            min="2000" max="2100">
                        @error('period_year')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="period_month">
                            {{ __('app.label_month') }} <span class="text-danger">*</span>
                        </label>
                        <select name="period_month" id="period_month"
                            class="form-control @error('period_month') is-invalid @enderror">
                            @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}"
                                {{ old('period_month', now()->month) == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                            @endforeach
                        </select>
                        @error('period_month')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="alert_threshold">
                            {{ __('app.label_alert_threshold') }}
                        </label>
                        <div class="input-group">
                            <input type="number" name="alert_threshold"
                                id="alert_threshold"
                                step="0.05" min="0.05" max="1"
                                class="form-control @error('alert_threshold') is-invalid @enderror"
                                value="{{ old('alert_threshold', 0.80) }}">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    (0.80 = 80%)
                                </span>
                            </div>
                            @error('alert_threshold')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted">
                            Se enviará una alerta cuando alcances este porcentaje del límite.
                        </small>
                    </div>
                </div>

            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> {{ __('app.save_budget') }}
            </button>
            <a href="{{ route('budgets.index') }}" class="btn btn-secondary ml-2">
                {{ __('app.cancel') }}
            </a>
        </div>
    </form>
</div>

@endsection