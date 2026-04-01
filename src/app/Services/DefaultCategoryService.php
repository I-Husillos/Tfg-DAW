<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;

/**
 * DefaultCategoryService
 * Crea el conjunto de categorías
 * predeterminadas para un usuario recién registrado.
 */
class DefaultCategoryService
{
    /**
     * Crea las categorías predeterminadas para un usuario.
     *
     * @param  User  $user  El usuario recién creado.
     */
    public function createFor(User $user): void
    {
        // ── Categorías de ingreso ─────────────────────────────────
        $incomeCategories = [
            ['name' => 'Salario',          'display_name' => 'Salario'],
            ['name' => 'Freelance',        'display_name' => 'Freelance'],
            ['name' => 'Alquiler cobrado', 'display_name' => 'Alquiler cobrado'],
            ['name' => 'Inversiones',      'display_name' => 'Inversiones'],
            ['name' => 'Otros ingresos',   'display_name' => 'Otros ingresos'],
        ];

        foreach ($incomeCategories as $data) {
            Category::create([
                'user_id'      => $user->id,
                'name'         => $data['name'],
                'display_name' => $data['display_name'],
                'type'         => 'income',
            ]);
        }

        // ── Categorías de gasto ───────────────────────────────────
        //
        // Guardamos en variables las que tendrán subcategorías.
        // Category::create() devuelve el modelo con el ID ya
        // asignado por la BD, por lo que no hace falta volver
        // a consultarlas. Esto elimina 2 queries innecesarias.
        $expenseCategories = [
            'Alimentación' => ['display_name' => 'Alimentación',          'has_children' => false],
            'Transporte'   => ['display_name' => 'Transporte',            'has_children' => true],
            'Vivienda'     => ['display_name' => 'Vivienda',              'has_children' => true],
            'Salud'        => ['display_name' => 'Salud',                 'has_children' => false],
            'Educación'    => ['display_name' => 'Educación',             'has_children' => false],
            'Ocio'         => ['display_name' => 'Ocio y entretenimiento','has_children' => false],
            'Ropa'         => ['display_name' => 'Ropa y calzado',        'has_children' => false],
            'Tecnología'   => ['display_name' => 'Tecnología',            'has_children' => false],
            'Seguros'      => ['display_name' => 'Seguros',               'has_children' => false],
            'Otros gastos' => ['display_name' => 'Otros gastos',          'has_children' => false],
        ];

        // Guardamos los modelos de las categorías padre que
        // necesitaremos para enlazar sus subcategorías.
        $parents = [];

        foreach ($expenseCategories as $name => $meta) {
            $category = Category::create([
                'user_id'      => $user->id,
                'name'         => $name,
                'display_name' => $meta['display_name'],
                'type'         => 'expense',
            ]);

            // Solo guardamos referencia si tiene hijos.
            // Así el array $parents es pequeño y claro.
            if ($meta['has_children']) {
                $parents[$name] = $category;
            }
        }

        // ── Subcategorías de Transporte ───────────────────────────
        foreach (['Gasolina', 'Transporte público', 'Parking', 'Mantenimiento vehículo'] as $nombre) {
            Category::create([
                'user_id'   => $user->id,
                'parent_id' => $parents['Transporte']->id,
                'name'      => $nombre,
                'type'      => 'expense',
            ]);
        }

        // ── Subcategorías de Vivienda ─────────────────────────────
        foreach (['Alquiler', 'Hipoteca', 'Suministros', 'Comunidad'] as $nombre) {
            Category::create([
                'user_id'   => $user->id,
                'parent_id' => $parents['Vivienda']->id,
                'name'      => $nombre,
                'type'      => 'expense',
            ]);
        }
    }
}