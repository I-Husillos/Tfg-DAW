<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        // Categorías de ingreso predefinidas
        $income = [
            ['name' => 'Salario',           'display_name' => 'Salario'],
            ['name' => 'Freelance',         'display_name' => 'Freelance'],
            ['name' => 'Alquiler cobrado',  'display_name' => 'Alquiler cobrado'],
            ['name' => 'Inversiones',       'display_name' => 'Inversiones'],
            ['name' => 'Otros ingresos',    'display_name' => 'Otros ingresos'],
        ];

        // Categorías de gasto predefinidas
        // Algunas tienen subcategorías definidas
        // más abajo para mostrar la jerarquía.
        $expense = [
            ['name' => 'Alimentación',  'display_name' => 'Alimentación'],
            ['name' => 'Transporte',    'display_name' => 'Transporte'],
            ['name' => 'Vivienda',      'display_name' => 'Vivienda'],
            ['name' => 'Salud',         'display_name' => 'Salud'],
            ['name' => 'Educación',     'display_name' => 'Educación'],
            ['name' => 'Ocio',          'display_name' => 'Ocio y entretenimiento'],
            ['name' => 'Ropa',          'display_name' => 'Ropa y calzado'],
            ['name' => 'Tecnología',    'display_name' => 'Tecnología'],
            ['name' => 'Seguros',       'display_name' => 'Seguros'],
            ['name' => 'Otros gastos',  'display_name' => 'Otros gastos'],
        ];

        foreach ($income as $data) {
            Category::create([
                'user_id'      => $user->id,
                'name'         => $data['name'],
                'display_name' => $data['display_name'],
                'type'         => 'income',
            ]);
        }

        foreach ($expense as $data) {
            Category::create([
                'user_id'      => $user->id,
                'name'         => $data['name'],
                'display_name' => $data['display_name'],
                'type'         => 'expense',
            ]);
        }

        // Subcategorías de ejemplo para Transporte.
        // parent_id apunta a la categoría padre.
        // El usuario puede añadir las suyas propias
        // desde el módulo de Categorías.
        $transporte = Category::where('user_id', $user->id)
            ->where('name', 'Transporte')
            ->first();

        $subcategoriasTransporte = [
            'Gasolina',
            'Transporte público',
            'Parking',
            'Mantenimiento vehículo',
        ];

        foreach ($subcategoriasTransporte as $nombre) {
            Category::create([
                'user_id'   => $user->id,
                'parent_id' => $transporte->id,
                'name'      => $nombre,
                'type'      => 'expense',
            ]);
        }

        // Subcategorías de ejemplo para Vivienda.
        $vivienda = Category::where('user_id', $user->id)
            ->where('name', 'Vivienda')
            ->first();

        $subcategoriasVivienda = [
            'Alquiler',
            'Hipoteca',
            'Suministros',
            'Comunidad',
        ];

        foreach ($subcategoriasVivienda as $nombre) {
            Category::create([
                'user_id'   => $user->id,
                'parent_id' => $vivienda->id,
                'name'      => $nombre,
                'type'      => 'expense',
            ]);
        }
    }
}