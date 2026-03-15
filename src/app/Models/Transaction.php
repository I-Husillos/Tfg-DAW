<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    // Sin account_id: eliminamos accounts.
    // Sin import_id: eliminamos imports.
    // Se añaden name, merchant, currency, date, meta
    // que estaban en la migración pero no en el modelo.
    // transaction_date se renombra a date para
    // coincidir con el nombre real del campo.
    protected $fillable = [
        'user_id',
        'category_id',
        'type',
        'amount',
        'currency',
        'date',
        'name',
        'merchant',
        'description',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date'   => 'datetime',
            'meta'   => 'array',
        ];
    }

    // La transacción pertenece al usuario.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // La transacción pertenece a una categoría.
    // Nullable: puede estar sin categorizar.
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Indica si esta transacción es un ingreso.
    public function isIncome(): bool
    {
        return $this->type === 'income';
    }

    // Indica si esta transacción es un gasto.
    public function isExpense(): bool
    {
        return $this->type === 'expense';
    }
}