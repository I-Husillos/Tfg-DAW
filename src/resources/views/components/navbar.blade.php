{{--
    Navbar superior de AdminLTE.
    Sin roles ni badges: monousuario, un único perfil.
    El username viene de auth()->user()->username
    porque 'name' ya no existe en la tabla users.
--}}
<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    {{-- Toggle del sidebar --}}
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    {{-- Elementos derecha --}}
    <ul class="navbar-nav ml-auto">

        {{-- Username del usuario autenticado --}}
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('profile.edit') }}" class="nav-link">
                <i class="fas fa-user-circle mr-1"></i>
                {{ auth()->user()->username }}
            </a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
            <span class="nav-link text-muted">|</span>
        </li>

        {{-- Badge de alertas de presupuesto --}}
        @if($budgetAlertCount > 0)
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#" role="button">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    <span class="badge badge-warning navbar-badge">
                        {{ $budgetAlertCount }}
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg">
                    <span class="dropdown-item dropdown-header">
                        {{ $budgetAlertCount }}
                        {{ $budgetAlertCount === 1 ? __('app.budget_alert_singular') : __('app.budget_alert_plural') }}
                    </span>
                    <div class="dropdown-divider"></div>
                    @foreach($budgetAlerts as $alert)
                        <a href="{{ route('budgets.index') }}" class="dropdown-item">
                            <i class="fas fa-wallet mr-2
                                {{ $alert['exceeded'] ? 'text-danger' : 'text-warning' }}">
                            </i>
                            <span class="{{ $alert['exceeded'] ? 'text-danger' : 'text-warning' }}">
                                {{ $alert['category'] }}
                            </span>
                            <span class="float-right text-muted text-sm">
                                {{ $alert['percentage'] }}%
                            </span>
                        </a>
                        <div class="dropdown-divider"></div>
                    @endforeach
                    <a href="{{ route('budgets.index') }}" class="dropdown-item dropdown-footer">
                        {{ __('app.view_all_budgets') }}
                    </a>
                </div>
            </li>
        @endif

        {{-- Logout con formulario POST --}}
        <li class="nav-item">
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="nav-link btn btn-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="d-none d-sm-inline ml-1">{{ __('app.logout') }}</span>
                </button>
            </form>
        </li>

    </ul>
</nav>