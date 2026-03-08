<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SmartBudget') - {{ config('app.name', 'SmartBudget') }}</title>

    {{--
        @vite carga los assets compilados por Vite.
        Lee public/build/manifest.json para saber qué archivo
        con hash corresponde a cada entrada, sin tocar las vistas.
    --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

{{--
    @yield('body-class') permite que cada vista hija defina
    la clase del body. AdminLTE necesita clases específicas
    para sus páginas de login/register ('login-page', 'register-page').
--}}
<body class="@yield('body-class', 'login-page')">
    @yield('content')
</body>

</html>