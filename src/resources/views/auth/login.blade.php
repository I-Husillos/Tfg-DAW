{{--
    Extiende el layout base de autenticación.
    Solo hereda el HTML base, los assets y el body-class.
--}}
@extends('layouts.auth')

@section('title', 'Iniciar sesión')

{{-- AdminLTE necesita esta clase en el body para aplicar su estilo de página de login --}}
@section('body-class', 'landing-page hold-transition')

@section('content')

    {{-- ── Hero Section ─────────────────────────────────────────────────────── --}}
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">

                {{-- Columna izquierda: presentación de la app --}}
                <div class="col-lg-7 mb-4 mb-lg-0">
                    <div class="mb-4">

                        <h1 class="display-4 font-weight-bold mb-3">
                            <i class="fas fa-wallet text-primary"></i>
                            <b>Smart</b>Budget
                        </h1>

                        <p class="lead mb-4">
                            Tu gestor financiero personal. Controla ingresos, gastos
                            y presupuestos desde un único panel, con informes claros
                            y acceso seguro a tus datos en todo momento.
                        </p>

                        {{-- Tres características destacadas de la app --}}
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="text-center">
                                    <div class="display-4 text-primary mb-2">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <h6 class="font-weight-bold">Análisis financiero</h6>
                                    <small class="text-muted">
                                        Dashboard con gráficos e informes mensuales exportables.
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="text-center">
                                    <div class="display-4 text-success mb-2">
                                        <i class="fas fa-file-csv"></i>
                                    </div>
                                    <h6 class="font-weight-bold">Importación CSV</h6>
                                    <small class="text-muted">
                                        Carga movimientos masivos desde tu banco en segundos.
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="text-center">
                                    <div class="display-4 text-warning mb-2">
                                        <i class="fas fa-piggy-bank"></i>
                                    </div>
                                    <h6 class="font-weight-bold">Presupuestos</h6>
                                    <small class="text-muted">
                                        Define límites por categoría y recibe alertas al superarlos.
                                    </small>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Columna derecha: formulario de login --}}
                <div class="col-lg-5">
                    <div class="login-box w-100">

                        {{--
                            card-outline card-primary: estilo AdminLTE con borde superior azul.
                            No necesita clases adicionales de Tailwind — AdminLTE lo gestiona.
                        --}}
                        <div class="card card-outline card-primary shadow">
                            <div class="card-header text-center">
                                <h4 class="mb-0">Accede a tu cuenta</h4>
                            </div>

                            <div class="card-body">

                                {{--
                                    $errors es una variable que Laravel inyecta automáticamente
                                    en todas las vistas cuando el FormRequest devuelve errores.
                                    No necesitas pasarla manualmente desde el controlador.
                                --}}
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

                                {{-- session('status') lo usa Laravel para mensajes de éxito --}}
                                @if (session('status'))
                                    <div class="alert alert-success">
                                        {{ session('status') }}
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('login') }}">
                                    {{--
                                        @csrf genera un campo hidden con el token CSRF.
                                        Laravel lo verifica en cada POST para proteger
                                        contra ataques Cross-Site Request Forgery.
                                        Sin esto el servidor devuelve error 419.
                                    --}}
                                    @csrf

                                    <div class="input-group mb-3">
                                        <input
                                            type="email"
                                            name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            placeholder="Email"
                                            value="{{ old('email') }}"
                                            required
                                            autofocus
                                        >
                                        {{--
                                            old('email') recupera el valor que el usuario escribió
                                            antes de que fallara la validación, evitando que tenga
                                            que volver a escribirlo.
                                        --}}
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

                                    <div class="row mb-3">
                                        <div class="col-8">
                                            <div class="icheck-primary">
                                                <input type="checkbox" id="remember" name="remember">
                                                <label for="remember">Recuérdame</label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <button type="submit" class="btn btn-primary btn-block">
                                                Entrar
                                            </button>
                                        </div>
                                    </div>

                                </form>

                                <p class="text-center mt-2 mb-0">
                                    ¿No tienes cuenta?
                                    <a href="{{ route('register') }}">Regístrate</a>
                                </p>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ── Features Section ─────────────────────────────────────────────────── --}}
    {{--
        Sección informativa inferior, igual que en tickets-main.
        Usa clases de AdminLTE/Bootstrap 4, sin JS ni lógica.
    --}}
    <section class="py-5 bg-primary">
        <div class="container">
            <h2 class="text-center font-weight-bold text-white">
                ¿Por qué SmartBudget?
            </h2>
            <hr class="mt-3 mb-5 mx-auto d-block bg-white" style="width:50px; border-width:3px;">

            <div class="row text-white text-center">
                <div class="col-md-3 mb-4">
                    <i class="fas fa-shield-alt fa-3x mb-3"></i>
                    <h5 class="font-weight-bold">Privacidad total</h5>
                    <p class="small">Tus datos financieros se procesan en local, sin servicios externos.</p>
                </div>
                <div class="col-md-3 mb-4">
                    <i class="fas fa-lock fa-3x mb-3"></i>
                    <h5 class="font-weight-bold">Acceso seguro</h5>
                    <p class="small">Autenticación robusta con roles y control de acceso granular.</p>
                </div>
                <div class="col-md-3 mb-4">
                    <i class="fas fa-tachometer-alt fa-3x mb-3"></i>
                    <h5 class="font-weight-bold">Rendimiento</h5>
                    <p class="small">Colas con Redis para importaciones y tareas pesadas sin bloqueos.</p>
                </div>
                <div class="col-md-3 mb-4">
                    <i class="fas fa-file-pdf fa-3x mb-3"></i>
                    <h5 class="font-weight-bold">Informes PDF</h5>
                    <p class="small">Exporta resúmenes mensuales detallados con un solo clic.</p>
                </div>
            </div>
        </div>
    </section>

@endsection