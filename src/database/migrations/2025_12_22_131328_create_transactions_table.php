<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // El núcleo del sistema. Cada registro es
        // un movimiento de dinero: ingreso o gasto.
        // Sin cuentas no existe el tipo transfer.
        // category_id nullable: se puede registrar
        // sin categoría y asignarla después.
        // merchant: nombre del comercio o pagador.
        // meta JSON: datos extra sin alterar el esquema.
        // Índice (user_id, date): acelera todas las
        // consultas de gráficos y reportes que filtran
        // por usuario y rango de fechas.
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->enum('type', ['income', 'expense']);
            $table->decimal('amount', 15, 2);
            $table->char('currency', 3)->default('EUR');
            $table->dateTime('date');
            $table->string('name', 150)->nullable();
            $table->string('merchant', 150)->nullable();
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};