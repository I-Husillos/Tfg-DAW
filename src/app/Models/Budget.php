<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'period_year',
        'period_month',
        'limit_amount',
        'alert_threshold',
    ];

    protected function casts(): array
    {
        return [
            'limit_amount'    => 'decimal:2',
            'alert_threshold' => 'decimal:2',
        ];
    }

    // El presupuesto pertenece al usuario.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // El presupuesto está asociado a una categoría.
    // Siempre será de tipo expense porque no tiene
    // sentido presupuestar ingresos.
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Calcula el gasto real acumulado en este
    // presupuesto sumando las transacciones del
    // mismo usuario, categoría y período.
    public function spentAmount(): float
    {
        return Transaction::where('user_id', $this->user_id)
            ->where('category_id', $this->category_id)
            ->where('type', 'expense')
            ->whereYear('date', $this->period_year)
            ->whereMonth('date', $this->period_month)
            ->sum('amount');
    }

    // Calcula el porcentaje consumido del presupuesto.
    // Devuelve un valor entre 0 y 1.
    // Ejemplo: 0.75 significa que lleva el 75% gastado.
    public function spentPercentage(): float
    {
        if ($this->limit_amount <= 0) {
            return 0;
        }

        return min($this->spentAmount() / $this->limit_amount, 1);
    }

    // Indica si se ha superado el umbral de alerta.
    // Se usa para disparar la notificación al usuario.
    public function hasReachedThreshold(): bool
    {
        return $this->spentPercentage() >= $this->alert_threshold;
    }
}