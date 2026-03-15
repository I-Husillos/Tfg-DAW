<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    // Se añaden display_name y description que
    // ahora existen en la tabla.
    protected $fillable = [
        'user_id',
        'parent_id',
        'name',
        'display_name',
        'description',
        'type',
    ];

    // La categoría pertenece a un usuario.
    // En monousuario siempre será el único usuario.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relación con la categoría padre.
    // Si parent_id es null, esta categoría
    // es de primer nivel (no tiene padre).
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Subcategorías de esta categoría.
    // Ejemplo: "Transporte" tiene children
    // "Gasolina", "Parking", "Transporte público".
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Transacciones clasificadas con esta categoría.
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Presupuestos definidos para esta categoría.
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    // Indica si esta categoría es de primer nivel.
    // Útil para filtrar en vistas y selectores.
    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    // Indica si esta categoría tiene subcategorías.
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }
}