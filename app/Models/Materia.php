<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use HasFactory;

    protected $table = 'materias';

    protected $primaryKey = 'IdMateria';

    public $timestamps = false;

    protected $fillable = [
        'IdMateriaPrevia',
        'CodigoMateria',
        'Nombre',
        'FechaRegistro',
        'Estado',
        'Descripcion',
    ];

    protected function casts(): array
    {
        return [
            'FechaRegistro' => 'datetime',
            'Estado' => 'boolean',
        ];
    }
}
