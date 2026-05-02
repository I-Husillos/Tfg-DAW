{{--
    Sidebar de AdminLTE para SmartBudget.
    Sin cuentas: eliminadas del proyecto.
    Sin roles: monousuario.
    request()->routeIs() detecta la ruta activa
    para aplicar la clase 'active' automáticamente.
--}}
<aside class="main-sidebar sidebar-dark-primary elevation-4">

    {{-- Brand --}}
    <a href="{{ route('dashboard') }}" class="brand-link">
        <i class="fas fa-wallet brand-image ml-2" style="font-size:1.8rem;opacity:.9"></i>
        <span class="brand-text font-weight-bold ml-2">SmartBudget</span>
    </a>

    <div class="sidebar">

        {{-- Panel del usuario --}}
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <i class="fas fa-user-circle fa-2x text-white ml-1"></i>
            </div>
            <div class="info">
                {{-- username porque 'name' ya no existe en users --}}
                <a href="{{ route('profile.edit') }}" class="d-block text-white">
                    {{ auth()->user()->username }}
                </a>
                <small class="text-white-50">{{ auth()->user()->email }}</small>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview"
                role="menu"
                data-accordion="false">

                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>{{ __('app.sidebar_dashboard') }}</p>
                    </a>
                </li>

                {{-- Transacciones --}}
                <li class="nav-item">
                    <a href="{{ route('transactions.index') }}"
                       class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-exchange-alt"></i>
                        <p>{{ __('app.sidebar_transactions') }}</p>
                    </a>
                </li>

                {{-- Categorías --}}
                <li class="nav-item">
                    <a href="{{ route('categories.index') }}"
                       class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>{{ __('app.sidebar_categories') }}</p>
                    </a>
                </li>

                {{-- Presupuestos --}}
                <li class="nav-item">
                    <a href="{{ route('budgets.index') }}"
                       class="nav-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-wallet"></i>
                        <p>{{ __('app.sidebar_budgets') }}</p>
                    </a>
                </li>

                {{-- Informes --}}
                <li class="nav-item">
                    <a href="{{ route('reports.index') }}"
                       class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>{{ __('app.sidebar_reports') }}</p>
                    </a>
                </li>

                {{-- Separador --}}
                <li class="nav-header">{{ __('app.sidebar_account') }}</li>

                {{-- Perfil --}}
                <li class="nav-item">
                    <a href="{{ route('profile.edit') }}"
                       class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>{{ __('app.sidebar_profile') }}</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>