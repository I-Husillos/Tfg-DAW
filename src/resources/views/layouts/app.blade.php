<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{--
        meta csrf-token es necesario para que las peticiones AJAX
        puedan enviar el token CSRF sin necesidad de un formulario.
        Cuando implementemos DataTables y fetch() lo usarán.
    --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SmartBudget') - {{ config('app.name', 'SmartBudget') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{--
        @stack('styles') permite que las vistas hijas inyecten
        CSS adicional en el <head> sin modificar el layout.
        Ejemplo de uso en una vista hija:
        @push('styles')
            <link rel="stylesheet" href="...">
        @endpush
    --}}
    @stack('styles')
</head>

{{--
    Clases de AdminLTE para el layout fijo con sidebar:
    - hold-transition: evita la animación de carga inicial
    - sidebar-mini: sidebar colapsable
    - layout-fixed: header y sidebar fijos al hacer scroll
--}}

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        {{--
        Navbar y Sidebar se extraen a componentes independientes
        siguiendo el patrón de tickets-main y el principio SRP:
        cada archivo tiene una única responsabilidad.
    --}}
        @include('components.navbar')
        @include('components.sidebar')

        {{-- Área de contenido principal --}}
        <div class="content-wrapper">

            {{-- Cabecera de página con título y breadcrumb --}}
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('title', 'Dashboard')</h1>
                        </div>
                        <div class="col-sm-6">
                            {{--
                            @stack('breadcrumb') permite que cada vista
                            inyecte su propio breadcrumb de navegación.
                        --}}
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}">Inicio</a>
                                </li>
                                @stack('breadcrumb')
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contenido principal de cada vista --}}
            <section class="content">
                <div class="container-fluid">
                    {{--
                    Mensajes de sesión globales — disponibles en cualquier
                    vista que extienda este layout sin repetir código.
                --}}
                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                        {{ session('success') }}
                    </div>
                    @endif

                    @if (session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                        {{ session('error') }}
                    </div>
                    @endif

                    @yield('content')
                </div>
            </section>
        </div>

        {{-- Footer --}}
        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Versión</b> 1.0
            </div>
            <strong>&copy; {{ date('Y') }} SmartBudget.</strong> Todos los derechos reservados.
        </footer>

        {{--
        @stack('scripts') al final del body para que los scripts
        de las vistas hijas se carguen después del DOM y de app.js.
    --}}
        @stack('scripts')

    </div>

    @stack('scripts')
    @include('partials.ai-chat-widget')
</body>

</html>