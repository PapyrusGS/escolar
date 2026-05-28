<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $primaryKey = 'IdRol';

    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'Descripcion',
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
