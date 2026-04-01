<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\DefaultCategoryService;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Crea las categorías predeterminadas para el usuario del seeder.
     */
    public function run(): void
    {
        $user = User::first();

        // Toda la lógica de categorías y subcategorías
        // vive en DefaultCategoryService.
        // El seeder solo orquesta: obtén el usuario → llama al servicio.
        app(DefaultCategoryService::class)->createFor($user);
    }
}