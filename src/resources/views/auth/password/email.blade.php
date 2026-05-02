@extends('layouts.auth')

@section('title', __('app.forgot_title'))
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
                {{ __('app.forgot_instruction') }}
            </p>

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="input-group mb-3">
                    <input
                        type="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="{{ __('app.email_placeholder') }}"
                        value="{{ old('email') }}"
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

                <button type="submit" class="btn btn-primary btn-block">
                {{ __('app.forgot_submit') }}
                </button>
            </form>

            <p class="text-center mt-3 mb-0">
                <a href="{{ route('login') }}">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('app.back_to_login') }}
                </a>
            </p>

        </div>
    </div>
</div>
@endsection