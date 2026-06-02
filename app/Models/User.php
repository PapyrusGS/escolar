<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $primaryKey = 'IdUsuario';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdRol',
        'Nombre1',
        'Nombre2',
        'Apellido1',
        'Apellido2',
        'CI',
        'Telefono',
        'Correo',
        'CorreoPersonal',
        'Contrasena',
        'FechaRegistro',
        'IdCarrera',
        'Semestre',
        'Estado',
    ];

    protected $hidden = [
        'Contrasena',
    ];

    protected function casts(): array
    {
        return [
            'FechaRegistro' => 'datetime',
            'Estado' => 'boolean',
        ];
    }

    public function getAuthPassword(): string
    {
        return (string) $this->Contrasena;
    }

    public function getRouteKeyName(): string
    {
        return 'IdUsuario';
    }

    public function scopeActive($query)
    {
        return $query->where('Estado', true);
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'IdRol', 'IdRol');
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class, 'IdEstudiante', 'IdUsuario');
    }
}
