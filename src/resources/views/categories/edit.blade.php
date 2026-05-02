@extends('layouts.app')

@section('title', __('app.edit_category') . ' ' . ($category->display_name ?? $category->name))

@push('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('categories.index') }}">{{ __('app.categories') }}</a>
</li>
<li class="breadcrumb-item active">{{ __('app.edit') }}</li>
@endpush

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-edit mr-1"></i>
            {{ __('app.edit_category') }} {{ $category->display_name ?? $category->name }}
        </h3>
    </div>
    <form action="{{ route('categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">{{ __('app.label_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $category->name) }}">
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
                            value="{{ old('display_name', $category->display_name) }}">
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
                            <option value="income"
                                {{ old('type', $category->type) === 'income' ? 'selected' : '' }}>
                                {{ __('app.income') }}
                            </option>
                            <option value="expense"
                                {{ old('type', $category->type) === 'expense' ? 'selected' : '' }}>
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
                        <label for="parent_id">{{ __('app.label_parent') }}</label>
                        <select name="parent_id" id="parent_id"
                            class="form-control @error('parent_id') is-invalid @enderror">
                            <option value="">{{ __('app.no_parent') }}</option>
                            @foreach($parents as $parent)
                            <option value="{{ $parent->id }}"
                                {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                [{{ $parent->type === 'income' ? __('app.income') : __('app.expense') }}]
                                {{ $parent->display_name ?? $parent->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="description">{{ __('app.label_description') }}</label>
                        <textarea name="description" id="description" rows="2"
                            class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
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
            <a href="{{ route('categories.index') }}" class="btn btn-secondary ml-2">
                {{ __('app.cancel') }}
            </a>
        </div>
    </form>
</div>

@endsection