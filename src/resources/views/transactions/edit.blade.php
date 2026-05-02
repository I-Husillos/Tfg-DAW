@extends('layouts.app')

@section('title', __('app.edit_transaction'))

@push('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('transactions.index') }}">{{ __('app.transactions') }}</a>
</li>
<li class="breadcrumb-item active">{{ __('app.edit') }}</li>
@endpush

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-edit mr-1"></i> {{ __('app.edit_transaction') }}
        </h3>
    </div>
    {{-- PUT mediante _method porque HTML solo
             soporta GET y POST en formularios. --}}
    <form action="{{ route('transactions.update', $transaction) }}"
        method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type">{{ __('app.label_type') }} <span class="text-danger">*</span></label>
                        <select name="type" id="type"
                            class="form-control @error('type') is-invalid @enderror">
                            <option value="income"
                                {{ old('type', $transaction->type) === 'income' ? 'selected' : '' }}>
                                {{ __('app.income') }}
                            </option>
                            <option value="expense"
                                {{ old('type', $transaction->type) === 'expense' ? 'selected' : '' }}>
                                {{ __('app.expense') }}
                            </option>
                        </select>
                        @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category_id">{{ __('app.label_category') }}</label>
                        <select name="category_id" id="category_id"
                            data-category-select
                            data-placeholder="{{ __('app.search_category') }}"
                            class="form-control @error('category_id') is-invalid @enderror">
                            <option value="">{{ __('app.no_category') }}</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $transaction->category_id) == $category->id ? 'selected' : '' }}>
                                [{{ $category->type === 'income' ? __('app.income') : __('app.expense') }}]
                                {{ $category->display_name ?? $category->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="amount">{{ __('app.label_amount') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="amount" id="amount"
                                step="0.01" min="0.01"
                                class="form-control @error('amount') is-invalid @enderror"
                                value="{{ old('amount', $transaction->amount) }}">
                            <div class="input-group-append">
                                <span class="input-group-text">{{ user_currency() }}</span>
                            </div>
                            @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="currency">{{ __('app.label_currency') }} <span class="text-danger">*</span></label>
                        <select name="currency" id="currency"
                            class="form-control @error('currency') is-invalid @enderror">
                            <option value="EUR" {{ old('currency', $transaction->currency) === 'EUR' ? 'selected' : '' }}>EUR — Euro</option>
                            <option value="USD" {{ old('currency', $transaction->currency) === 'USD' ? 'selected' : '' }}>USD — Dólar</option>
                            <option value="GBP" {{ old('currency', $transaction->currency) === 'GBP' ? 'selected' : '' }}>GBP — Libra</option>
                        </select>
                        @error('currency')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="date">{{ __('app.label_date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="date" id="date"
                            class="form-control @error('date') is-invalid @enderror"
                            value="{{ old('date', $transaction->date->format('Y-m-d')) }}">
                        @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">{{ __('app.label_name') }}</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $transaction->name) }}">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="merchant">{{ __('app.label_merchant') }}</label>
                        <input type="text" name="merchant" id="merchant"
                            class="form-control @error('merchant') is-invalid @enderror"
                            value="{{ old('merchant', $transaction->merchant) }}">
                        @error('merchant')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="description">{{ __('app.label_description') }}</label>
                        <textarea name="description" id="description" rows="3"
                            class="form-control @error('description') is-invalid @enderror">{{ old('description', $transaction->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> {{ __('app.save_changes') }}
            </button>
            <a href="{{ route('transactions.index') }}" class="btn btn-secondary ml-2">
                {{ __('app.cancel') }}
            </a>
        </div>
    </form>
</div>

@endsection