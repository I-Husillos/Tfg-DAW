@extends('layouts.app')

@section('title', __('app.my_profile'))

@push('breadcrumb')
<li class="breadcrumb-item active">{{ __('app.my_profile') }}</li>
@endpush

@section('content')

<div class="row">

    {{-- Columna izquierda: datos personales                    --}}
    <div class="col-lg-8">

        {{-- Datos personales --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user mr-1"></i> {{ __('app.personal_data') }}
                </h3>
            </div>
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{ __('app.label_name') }}</label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $profile?->name) }}">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="surname">{{ __('app.label_surname') }}</label>
                                <input type="text" name="surname" id="surname"
                                    class="form-control @error('surname') is-invalid @enderror"
                                    value="{{ old('surname', $profile?->surname) }}">
                                @error('surname')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company">{{ __('app.label_company') }}</label>
                                <input type="text" name="company" id="company"
                                    class="form-control @error('company') is-invalid @enderror"
                                    value="{{ old('company', $profile?->company) }}">
                                @error('company')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">{{ __('app.label_phone') }}</label>
                                <input type="text" name="phone" id="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $profile?->phone) }}">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="address">{{ __('app.label_address') }}</label>
                                <input type="text" name="address" id="address"
                                    class="form-control @error('address') is-invalid @enderror"
                                    value="{{ old('address', $profile?->address) }}">
                                @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="postal_code">{{ __('app.label_postal_code') }}</label>
                                <input type="text" name="postal_code" id="postal_code"
                                    class="form-control @error('postal_code') is-invalid @enderror"
                                    value="{{ old('postal_code', $profile?->postal_code) }}">
                                @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="city">{{ __('app.label_city') }}</label>
                                <input type="text" name="city" id="city"
                                    class="form-control @error('city') is-invalid @enderror"
                                    value="{{ old('city', $profile?->city) }}">
                                @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="country">{{ __('app.label_country') }}</label>
                                <input type="text" name="country" id="country"
                                    class="form-control @error('country') is-invalid @enderror"
                                    value="{{ old('country', $profile?->country) }}">
                                @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>

                    <hr>
                    <h6 class="text-muted mb-3">{{ __('app.app_preferences') }}</h6>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="currency">
                                    {{ __('app.label_currency') }} <span class="text-danger">*</span>
                                </label>
                                <select name="currency" id="currency"
                                    class="form-control @error('currency') is-invalid @enderror">
                                    <option value="EUR" {{ old('currency', $profile?->currency) === 'EUR' ? 'selected' : '' }}>EUR — Euro</option>
                                    <option value="USD" {{ old('currency', $profile?->currency) === 'USD' ? 'selected' : '' }}>USD — Dólar</option>
                                    <option value="GBP" {{ old('currency', $profile?->currency) === 'GBP' ? 'selected' : '' }}>GBP — Libra</option>
                                </select>
                                @error('currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="language">
                                    {{ __('app.label_language') }} <span class="text-danger">*</span>
                                </label>
                                <select name="language" id="language"
                                    class="form-control @error('language') is-invalid @enderror">
                                    <option value="es" {{ old('language', $profile?->language) === 'es' ? 'selected' : '' }}>Español</option>
                                    <option value="en" {{ old('language', $profile?->language) === 'en' ? 'selected' : '' }}>English</option>
                                </select>
                                @error('language')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="timezone">
                                    {{ __('app.label_timezone') }} <span class="text-danger">*</span>
                                </label>
                                <select name="timezone" id="timezone"
                                    class="form-control @error('timezone') is-invalid @enderror">
                                    @foreach($timezones as $tz)
                                    <option value="{{ $tz }}"
                                        {{ old('timezone', $profile?->timezone) === $tz ? 'selected' : '' }}>
                                        {{ $tz }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('timezone')
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
                </div>
            </form>
        </div>

    </div>

    {{-- Columna derecha: credenciales y resumen --}}
    <div class="col-lg-4">

        {{-- Resumen de cuenta --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-id-card mr-1"></i> {{ __('app.account') }}
                </h3>
            </div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt>{{ __('app.account_user') }}</dt>
                    <dd class="text-muted">{{ $user->username }}</dd>
                    <dt>{{ __('app.account_email') }}</dt>
                    <dd class="text-muted">{{ $user->email }}</dd>
                    <dt>{{ __('app.member_since') }}</dt>
                    <dd class="text-muted">{{ $user->created_at->format('d/m/Y') }}</dd>
                </dl>
            </div>
        </div>

        {{-- Cambiar contraseña --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-lock mr-1"></i> {{ __('app.change_password') }}
                </h3>
            </div>
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">

                    <div class="form-group">
                        <label for="current_password">{{ __('app.current_password') }}</label>
                        <input type="password" name="current_password"
                            id="current_password"
                            class="form-control @error('current_password') is-invalid @enderror"
                            autocomplete="current-password">
                        @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">{{ __('app.new_password_placeholder') }}</label>
                        <input type="password" name="password"
                            id="password"
                            class="form-control @error('password') is-invalid @enderror"
                            autocomplete="new-password">
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">{{ __('app.confirm_new_password') }}</label>
                        <input type="password" name="password_confirmation"
                            id="password_confirmation"
                            class="form-control"
                            autocomplete="new-password">
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning btn-block">
                        <i class="fas fa-key mr-1"></i> {{ __('app.change_password') }}
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>

@endsection