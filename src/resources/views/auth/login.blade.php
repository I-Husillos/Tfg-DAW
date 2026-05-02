@extends('layouts.auth')

@section('title', __('app.login_title'))
@section('body-class', 'landing-page hold-transition')

@section('content')

{{-- Hero: presentación + formulario de login --}}
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">

            {{-- Columna izquierda: descripción de la app --}}
            <div class="col-lg-7 mb-4 mb-lg-0">
                <h1 class="display-4 font-weight-bold mb-3">
                    <i class="fas fa-wallet text-primary"></i>
                    <b>Smart</b>Budget
                </h1>
                <p class="lead mb-4">
                    {{ __('app.login_tagline') }}
                </p>
                <div class="row">
                    <div class="col-md-4 mb-3 text-center">
                        <i class="fas fa-chart-line fa-3x text-primary mb-2"></i>
                        <h6 class="font-weight-bold">{{ __('app.login_feature_analysis') }}</h6>
                        <small class="text-muted">{{ __('app.login_feature_analysis_desc') }}</small>
                    </div>
                    <div class="col-md-4 mb-3 text-center">
                        <i class="fas fa-tags fa-3x text-success mb-2"></i>
                        <h6 class="font-weight-bold">{{ __('app.login_feature_categories') }}</h6>
                        <small class="text-muted">{{ __('app.login_feature_categories_desc') }}</small>
                    </div>
                    <div class="col-md-4 mb-3 text-center">
                        <i class="fas fa-lock fa-3x text-warning mb-2"></i>
                        <h6 class="font-weight-bold">{{ __('app.login_feature_privacy') }}</h6>
                        <small class="text-muted">{{ __('app.login_feature_privacy_desc') }}</small>
                    </div>
                </div>
            </div>

            {{-- Columna derecha: formulario --}}
            <div class="col-lg-5">
                <div class="card card-outline card-primary shadow">
                    <div class="card-header text-center">
                        <h4 class="mb-0">{{ __('app.login_title') }}</h4>
                    </div>
                    <div class="card-body">

                        @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            {{-- El campo acepta email o username --}}
                            <div class="input-group mb-3">
                                <input
                                    type="text"
                                    name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="{{ __('app.email_or_username') }}"
                                    value="{{ old('email') }}"
                                    autofocus>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-user"></span>
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
                                    placeholder="{{ __('app.password') }}">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-8">
                                    <div class="icheck-primary">
                                        <input type="checkbox" id="remember" name="remember">
                                        <label for="remember">{{ __('app.remember_me') }}</label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        {{ __('app.login_submit') }}
                                    </button>
                                </div>
                            </div>
                        </form>

                        <p class="text-center mt-2 mb-1">
                            <a href="{{ route('password.request') }}">{{ __('app.forgot_password') }}</a>
                        </p>
                        <p class="text-center mb-0">
                            {{ __('app.no_account') }}
                            <a href="{{ route('register') }}">{{ __('app.register_link') }}</a>
                        </p>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection