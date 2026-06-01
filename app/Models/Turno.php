<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    use HasFactory;

    protected $table = 'turnos';

    protected $primaryKey = 'IdTurno';

    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'HoraInicio',
        'HoraFin',
        'Lun',
        'Mar',
        'Mie',
        'Jue',
        'Vie',
        'Sab',
        'Dom',
        'FechaRegistro',
        'Estado',
    ];

    protected function casts(): array
    {
        return [
            'FechaRegistro' => 'datetime',
            'Estado' => 'boolean',
            'Lun' => 'boolean',
            'Mar' => 'boolean',
            'Mie' => 'boolean',
            'Jue' => 'boolean',
            'Vie' => 'boolean',
            'Sab' => 'boolean',
            'Dom' => 'boolean',
        ];
    }
}
