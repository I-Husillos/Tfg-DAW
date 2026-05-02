<?php

namespace App\Providers;

use App\Services\BudgetAlertService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registramos el servicio en el contenedor
        // para poder inyectarlo donde sea necesario.
        $this->app->singleton(BudgetAlertService::class);
    }

    public function boot(): void
    {
        // Compartimos las alertas de presupuesto con
        // todas las vistas que usen el layout 'app'.
        // View::composer ejecuta el callback justo antes
        // de renderizar la vista especificada, así siempre
        // tiene los datos más actuales sin necesidad de
        // pasarlos manualmente desde cada controlador.
        View::composer('components.navbar', function ($view) {
            $service = app(BudgetAlertService::class);
            $view->with('budgetAlerts', $service->getActiveAlerts());
            $view->with('budgetAlertCount', $service->getAlertCount());
        });
    }
}