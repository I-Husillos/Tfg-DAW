@extends('layouts.auth')

@section('title', __('app.reset_title'))
@section('body-class', 'login-page hold-transition')

@section('content')
<div class="login-box">
    <div class="card card-outline card-primary shadow">
        <div class="card-header text-center">
            <h4 class="mb-0">
                <i class="fas fa-wallet text-primary mr-1"></i>
                <b>Smart</b>Budget
            </h4>
        </div>
        <div class="card-body">

            <p class="text-muted text-center mb-4">
                {{ __('app.reset_instruction') }}
            </p>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                {{-- Token oculto que Laravel usa para validar
                     que el enlace del email es legítimo y no
                     ha expirado. --}}
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="input-group mb-3">
                    <input
                        type="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="{{ __('app.email_placeholder') }}"
                        value="{{ old('email', $email ?? '') }}"
                        autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group mb-3">
                    <input
                        type="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="{{ __('app.new_password_placeholder') }}">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group mb-4">
                    <input
                        type="password"
                        name="password_confirmation"
                        class="form-control"
                        placeholder="{{ __('app.confirm_new_password') }}">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    {{ __('app.reset_submit') }}
                </button>
            </form>

        </div>
    </div>
</div>
@endsection