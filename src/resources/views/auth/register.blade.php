@extends('layouts.auth')

@section('title', __('app.register_title'))
@section('body-class', 'register-page hold-transition')

@section('content')

<section class="py-5">
    <div class="container">
        <div class="row align-items-center justify-content-center">

            <div class="col-lg-6 mb-4 mb-lg-0">
                <h1 class="display-4 font-weight-bold mb-3">
                    <i class="fas fa-wallet text-primary"></i>
                    <b>Smart</b>Budget
                </h1>
                <p class="lead">
                    {{ __('app.register_tagline') }}
                </p>
                <ul class="list-unstyled mt-4">
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        {{ __('app.register_feature_1') }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        {{ __('app.register_feature_2') }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        {{ __('app.register_feature_3') }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        {{ __('app.register_feature_4') }}
                    </li>
                </ul>
            </div>

            <div class="col-lg-5">
                <div class="card card-outline card-primary shadow">
                    <div class="card-header text-center">
                        <h4 class="mb-0">{{ __('app.register_card_title') }}</h4>
                    </div>
                    <div class="card-body">

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="input-group mb-3">
                                <input
                                    type="text"
                                    name="username"
                                    class="form-control @error('username') is-invalid @enderror"
                                    placeholder="{{ __('app.username_placeholder') }}"
                                    value="{{ old('username') }}"
                                    autofocus>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-user"></span>
                                    </div>
                                </div>
                                @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="input-group mb-3">
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="{{ __('app.email_placeholder') }}"
                                    value="{{ old('email') }}">
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
                                    placeholder="{{ __('app.password_placeholder') }}">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="input-group mb-3">
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    class="form-control"
                                    placeholder="{{ __('app.confirm_password_placeholder') }}">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-user-plus mr-1"></i> {{ __('app.register_submit') }}
                            </button>
                        </form>

                        <p class="text-center mt-3 mb-0">
                            {{ __('app.already_account') }}
                            <a href="{{ route('login') }}">{{ __('app.login_link') }}</a>
                        </p>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection