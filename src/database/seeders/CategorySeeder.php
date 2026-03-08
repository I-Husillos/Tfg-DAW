<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $incomeCategories = [
            'Salario',
            'Freelance',
            'Alquiler',
            'Inversiones',
            'Otros ingresos',
        ];

        $expenseCategories = [
            'Alimentación',
            'Transporte',
            'Vivienda',
            'Salud',
            'Educación',
            'Ocio',
            'Ropa',
            'Tecnología',
            'Seguros',
            'Otros gastos',
        ];

        // user_id = null significa categoría global, visible para todos los usuarios
        foreach ($incomeCategories as $name) {
            Category::create([
                'name'    => $name,
                'type'    => 'income',
                'user_id' => null,
            ]);
        }

        foreach ($expenseCategories as $name) {
            Category::create([
                'name'    => $name,
                'type'    => 'expense',
                'user_id' => null,
            ]);
        }
    }
}
