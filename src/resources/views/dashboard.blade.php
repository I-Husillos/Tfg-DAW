<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - SmartBudget</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger ml-2">
                        Cerrar sesión
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    <div class="content-wrapper p-4">
        <h1>Bienvenido, {{ auth()->user()->name }}</h1>
        <p>Rol: <strong>{{ auth()->user()->role }}</strong></p>
    </div>

</div>
</body>
</html>