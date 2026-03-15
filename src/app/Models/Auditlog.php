<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditLog extends Model
{
    use HasFactory;

    // Tabla append-only: solo se inserta, nunca
    // se actualiza ni se borra un registro existente.
    protected $fillable = [
        'action',
        'model',
        'model_id',
        'diff',
    ];

    protected function casts(): array
    {
        return [
            'diff' => 'array',
        ];
    }

    // No tiene relación directa con User porque en monousuario siempre es el mismo usuario.
    // No necesita FK para saber de quién es el log.
}