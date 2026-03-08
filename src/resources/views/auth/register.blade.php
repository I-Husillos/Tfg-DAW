@extends('layouts.auth')

@section('title', 'Crear cuenta')

@section('body-class', 'register-page hold-transition')

@section('content')

    <section class="py-5">
        <div class="container">
            <div class="row align-items-center justify-content-center">

                {{-- Columna izquierda: presentación --}}
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h1 class="display-4 font-weight-bold mb-3">
                        <i class="fas fa-wallet text-primary"></i>
                        <b>Smart</b>Budget
                    </h1>
                    <p class="lead">
                        Crea tu cuenta gratuita y empieza a tener el control
                        total de tus finanzas personales hoy mismo.
                    </p>
                    <ul class="list-unstyled mt-4">
                        <li class="mb-2">
                            <i class="fas fa-check text-success mr-2"></i>
                            Registro de ingresos y gastos ilimitados
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success mr-2"></i>
                            Dashboard con gráficos en tiempo real
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success mr-2"></i>
                            Importación desde CSV de cualquier banco
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success mr-2"></i>
                            Informes mensuales exportables en PDF
                        </li>
                    </ul>
                </div>

                {{-- Columna derecha: formulario de registro --}}
                <div class="col-lg-5">
                    <div class="register-box w-100">
                        <div class="card card-outline card-primary shadow">
                            <div class="card-header text-center">
                                <h4 class="mb-0">Crea tu cuenta</h4>
                            </div>

                            <div class="card-body">

                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert">
                                            <span>&times;</span>
                                        </button>
                                        @foreach ($errors->all() as $error)
                                            <div>{{ $error }}</div>
                                        @endforeach
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('register') }}">
                                    @csrf

                                    <div class="input-group mb-3">
                                        <input
                                            type="text"
                                            name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            placeholder="Nombre completo"
                                            value="{{ old('name') }}"
                                            required
                                            autofocus
                                        >
                                    </div>

                                    <div class="input-group mb-3">
                                        <input
                                            type="email"
                                            name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            placeholder="Email"
                                            value="{{ old('email') }}"
                                            required
                                        >
                                    </div>

                                    <div class="input-group mb-3">
                                        <input
                                            type="password"
                                            name="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="Contraseña"
                                            required
                                        >
                                    </div>

                                    <div class="input-group mb-3">
                                        {{--
                                            Este campo debe llamarse exactamente 'password_confirmation'.
                                            Laravel lo busca automáticamente al usar la regla 'confirmed'
                                            en el RegisterRequest.
                                        --}}
                                        <input
                                            type="password"
                                            name="password_confirmation"
                                            class="form-control"
                                            placeholder="Repite la contraseña"
                                            required
                                        >
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-user-plus mr-1"></i> Crear cuenta
                                    </button>

                                </form>

                                <p class="text-center mt-3 mb-0">
                                    ¿Ya tienes cuenta?
                                    <a href="{{ route('login') }}">Inicia sesión</a>
                                </p>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection