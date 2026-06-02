<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MateriaAprobada extends Model
{
    protected $table = 'materias_aprobadas';

    protected $primaryKey = 'IdMateriaAprobada';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdUsuario',
        'IdMateria',
        'IdCarrera',
        'NotaFinal',
        'FechaAprobacion',
        'Estado',
    ];

    protected function casts(): array
    {
        return [
            'NotaFinal' => 'float',
            'FechaAprobacion' => 'date',
            'Estado' => 'boolean',
        ];
    }
}
