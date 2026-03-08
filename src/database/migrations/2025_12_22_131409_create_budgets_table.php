<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
            // Porcentaje a partir del cual se dispara alerta (0.8 = 80%)
            $table->decimal('alert_threshold', 4, 2)->default(0.8);
            $table->timestamps();
            // Un presupuesto único por usuario, categoría y periodo
            $table->unique(['user_id', 'category_id', 'period_year', 'period_month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
