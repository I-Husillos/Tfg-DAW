<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'surname',
        'phone',
        'address',
        'city',
        'postal_code',
        'country',
        'company',
        'currency',
        'language',
        'timezone',
    ];

    // El perfil pertenece a un usuario.
    // Es la parte 1:1 desde el lado del perfil.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}