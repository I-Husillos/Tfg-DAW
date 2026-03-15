<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Sin 'role', 'timezone', 'currency': esos campos
    // ya no existen en users. Las preferencias viven
    // en profiles. No hay roles en monousuario.
    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // Relación 1:1 con profiles.
    // hasOne porque un usuario tiene exactamente
    // un perfil, nunca más de uno.
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    // Un usuario tiene muchas categorías.
    // En monousuario siempre serán las suyas.
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    // Un usuario tiene muchas transacciones.
    // Toda la actividad financiera pasa por aquí.
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Un usuario tiene muchos presupuestos.
    // Uno por categoría y período mensual.
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    // Un usuario tiene muchos registros de auditoría.
    // Se generan automáticamente al editar o eliminar
    // transacciones, presupuestos y categorías.
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}