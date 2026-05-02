@extends('layouts.app')

@section('title', __('app.new_category'))

@push('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('categories.index') }}">{{ __('app.categories') }}</a>
</li>
<li class="breadcrumb-item active">{{ __('app.new_category') }}</li>
@endpush

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-plus mr-1"></i> {{ __('app.new_category') }}
        </h3>
    </div>
    <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">{{ __('app.label_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}"
                            placeholder="Ej: Alimentación">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="display_name">{{ __('app.label_display_name') }}</label>
                        <input type="text" name="display_name" id="display_name"
                            class="form-control @error('display_name') is-invalid @enderror"
                            value="{{ old('display_name') }}"
                            placeholder="Ej: Alimentación y supermercado">
                        <small class="form-text text-muted">
                            {{ __('app.display_name_hint') }}
                        </small>
                        @error('display_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type">{{ __('app.col_type') }} <span class="text-danger">*</span></label>
                        <select name="type" id="type"
                            class="form-control @error('type') is-invalid @enderror">
                            <option value="">{{ __('app.select_type') }}</option>
                            <option value="income" {{ old('type') === 'income'  ? 'selected' : '' }}>{{ __('app.income') }}</option>
                            <option value="expense" {{ old('type') === 'expense' ? 'selected' : '' }}>{{ __('app.expense') }}</option>
                        </select>
                        @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="parent_id">{{ __('app.label_parent') }}</label>
                        <select name="parent_id" id="parent_id"
                            class="form-control @error('parent_id') is-invalid @enderror">
                            <option value="">{{ __('app.no_parent') }}</option>
                            @foreach($parents as $parent)
                            <option value="{{ $parent->id }}"
                                {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                [{{ $parent->type === 'income' ? __('app.income') : __('app.expense') }}]
                                {{ $parent->display_name ?? $parent->name }}
                            </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            {{ __('app.parent_hint') }}
                        </small>
                        @error('parent_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="description">{{ __('app.label_description') }}</label>
                        <textarea name="description" id="description" rows="2"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Descripción opcional de la categoría...">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> {{ __('app.save_category') }}
            </button>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary ml-2">
                {{ __('app.cancel') }}
            </a>
        </div>
    </form>
</div>

@endsection