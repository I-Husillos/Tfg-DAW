<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Historial de cambios en datos financieros.
        // action: qué ocurrió (created/updated/deleted)
        // model: sobre qué entidad (Transaction/Budget)
        // model_id: sobre qué registro concreto.
        // diff: qué valores cambiaron en JSON.
        // Ejemplo: editar transacción de 450€ a 400€
        // guarda diff: {"amount": [450, 400]}
        // Sin user_id: monousuario, siempre es el mismo.
        // Sin ip_address ni user_agent: no hay terceros.
        // Tabla append-only: solo se inserta, nunca
        // se actualiza ni se borra.
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action', 100);
            $table->string('model', 100);
            $table->unsignedBigInteger('model_id');
            $table->json('diff')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};