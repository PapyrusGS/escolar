<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;

    protected $table = 'cursos';

    protected $primaryKey = 'IdCurso';

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

    public function getRouteKeyName(): string
    {
        return 'IdCurso';
    }
}
