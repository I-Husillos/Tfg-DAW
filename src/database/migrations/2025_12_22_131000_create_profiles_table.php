<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Identidad y preferencias del usuario.
        // Separado de users por SRP: users gestiona
        // autenticación, profiles gestiona quién es
        // el usuario y cómo quiere usar la app.
        // Relación 1:1 con users (unique en user_id).
        // Se crea automáticamente al registrarse.
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();
            $table->string('name', 100)->nullable();
            $table->string('surname', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('company', 150)->nullable();
            $table->char('currency', 3)->default('EUR');
            $table->string('language', 5)->default('es');
            $table->string('timezone', 50)->default('Europe/Madrid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};