<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Límites de gasto mensuales por categoría.
        // Compara lo presupuestado con lo gastado
        // sumando transactions del mismo período.
        // alert_threshold: porcentaje de alerta,
        // por defecto 0.80 (avisa al 80% consumido).
        // unique(user_id, category_id, year, month):
        // no puede haber dos presupuestos para la
        // misma categoría en el mismo mes.
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->integer('period_year');
            $table->tinyInteger('period_month');
            $table->decimal('limit_amount', 15, 2);
            $table->decimal('alert_threshold', 4, 2)->default(0.80);
            $table->timestamps();

            $table->unique([
                'user_id',
                'category_id',
                'period_year',
                'period_month',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};