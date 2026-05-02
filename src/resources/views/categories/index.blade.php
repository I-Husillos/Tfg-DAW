@extends('layouts.app')

@section('title', __('app.categories'))

@push('breadcrumb')
    <li class="breadcrumb-item active">{{ __('app.categories') }}</li>
@endpush

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-tags mr-1"></i> {{ __('app.categories') }}
        </h3>
        <div class="card-tools">
            <a href="{{ route('categories.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-1"></i> {{ __('app.new_category') }}
            </a>
        </div>
    </div>
    <div class="card-body">

        <div class="row mb-3">
            <div class="col-md-3 col-sm-6 mb-2">
                <select id="filter-type" class="form-control">
                    <option value="">{{ __('app.filter_type_all') }}</option>
                    <option value="income">{{ __('app.filter_income') }}</option>
                    <option value="expense">{{ __('app.filter_expense') }}</option>
                </select>
            </div>
            <div class="col-md-3 col-sm-6 mb-2">
                <button id="clear-filters" class="btn btn-secondary btn-block">
                    <i class="fas fa-times mr-1"></i> {{ __('app.filter_clear_filters') }}
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="tabla-categorias"
                   class="table table-hover table-striped table-bordered mb-0 text-center dt-responsive"
                   data-api-url="{{ route('api.categories.index') }}">
                <thead class="text-center bg-white font-weight-bold">
                    <tr>
                        <th>{{ __('app.label_name') }}</th>
                        <th>{{ __('app.col_type') }}</th>
                        <th>{{ __('app.col_subcategories') }}</th>
                        <th>{{ __('app.label_description') }}</th>
                        <th>{{ __('app.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>
</div>

@endsection