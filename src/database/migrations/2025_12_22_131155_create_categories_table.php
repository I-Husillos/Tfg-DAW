<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Etiquetas para clasificar transacciones.
        // type distingue categorías de ingreso y gasto:
        // al crear una transacción income solo aparecen
        // categorías income, y al crear un presupuesto
        // (siempre gasto) solo aparecen categorías expense.
        // parent_id permite jerarquía de subcategorías:
        // "Transporte" → "Gasolina", "Parking".
        // unique(user_id, name): mismo usuario no puede
        // tener dos categorías con el mismo nombre.
        // Las categorías predefinidas se insertan via
        // seeder al instalar la app.
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();
            $table->string('name', 120);
            $table->string('display_name', 120)->nullable();
            $table->string('description')->nullable();
            $table->enum('type', ['income', 'expense']);
            $table->timestamps();

            $table->unique(['user_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};