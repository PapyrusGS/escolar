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
        'Dias',
        'FechaRegistro',
        'Estado',
    ];

    protected function casts(): array
    {
        return [
            'FechaRegistro' => 'datetime',
            'Estado' => 'boolean',
        ];
    }
}
