<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\BudgetAlertNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * Servicio responsable de DETECTAR y NOTIFICAR alertas de presupuesto.
 */
class BudgetNotificationService
{
    /**
     * Comprueba todos los presupuestos del usuario autenticado para el
     * mes en curso y envía un email si alguno ha cruzado el umbral
     * de alerta por primera vez.
     *
     * Este método se llama después de guardar una transacción de tipo
     * 'expense', que es el único momento en que un presupuesto puede cruzar su umbral.
     *
     * @param int $userId  ID del usuario propietario de la transacción
     */
    public function checkAndNotify(int $userId): void
    {
        $year  = now()->year;
        $month = now()->month;

        // Cargamos los presupuestos del mes actual del usuario junto con su categoría (para mostrar el nombre en el email)
        $budgets = Budget::where('user_id', $userId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->with('category')
            ->get();

        // Obtenemos el objeto User para poder enviarle la notificación.
        // No usamos Auth::user() porque este método podría llamarse desde un Job en cola, donde no hay contexto HTTP.
        $user = User::find($userId);

        if (! $user) {
            return;
        }

        foreach ($budgets as $budget) {
            $this->checkSingleBudget($budget, $user);
        }
    }

    /**
     * Comprueba UN presupuesto específico y envía el email si corresponde.
     */
    private function checkSingleBudget(Budget $budget, \App\Models\User $user): void
    {
        if (! $budget->hasReachedThreshold()) {
            // No ha cruzado el umbral, no hay nada que hacer.
            // Además, si había una notificación en caché de un mes anterior que "bajó" (caso raro pero posible si se borran transacciones), la limpiamos para que vuelva a notificar si sube de nuevo.
            return;
        }

        $cacheKey = $this->buildCacheKey($budget);

        // already_notified es true si ya enviamos un email este mes
        $alreadyNotified = Cache::has($cacheKey);

        if ($alreadyNotified) {
            // Ya notificamos este mes, no repetimos el email.
            return;
        }

        // Calculamos los valores para el email
        $spent      = $budget->spentAmount();
        $percentage = round($budget->spentPercentage() * 100, 2);

        // Enviamos la notificación al usuario.
        // Como BudgetAlertNotification implementa ShouldQueue, esto no bloqueará la petición HTTP — se procesa en segundo plano.
        $user->notify(new BudgetAlertNotification($budget, $spent, $percentage));

        // Marcamos en caché que ya se notificó este presupuesto este mes.
        // El tiempo de expiración es hasta el final del mes actual, así el mes siguiente la caché expirará sola y se podrá volver a notificar.
        Cache::put($cacheKey, true, $this->secondsUntilEndOfMonth());
    }

    /**
     * Construye una clave de caché única para cada presupuesto y mes.
     */
    private function buildCacheKey(Budget $budget): string
    {
        return sprintf(
            'budget_alert:%d:%d:%d:%d',
            $budget->user_id,
            $budget->id,
            now()->year,
            now()->month
        );
    }

    /**
     * Calcula los segundos que faltan hasta las 23:59:59 del último día del mes.
     * Esto asegura que la caché expira de forma natural al cambiar de mes.
     */
    private function secondsUntilEndOfMonth(): int
    {
        $endOfMonth = now()->endOfMonth();

        // Diferencia en segundos entre ahora y el fin del mes
        return (int) now()->diffInSeconds($endOfMonth);
    }
}