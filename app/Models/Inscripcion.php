<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripciones';

    protected $primaryKey = 'IdInscripcion';

    public $timestamps = false;

    protected $fillable = [
        'IdEstudiante',
        'IdCursoMateria',
        'Fecha',
        'Estado',
        'Aprobado',
    ];

    protected function casts(): array
    {
        return [
            'Fecha' => 'datetime',
            'Estado' => 'boolean',
            'Aprobado' => 'boolean',
        ];
    }

    public function cursoMateria(): BelongsTo
    {
        return $this->belongsTo(CursoMateria::class, 'IdCursoMateria', 'IdCursoMateria');
    }

    public function estudianteCarrera(): BelongsTo
    {
        return $this->belongsTo(EstudianteCarrera::class, 'IdEstudiante', 'IdEstudianteCarrera');
    }

    public function nota(): HasOne
    {
        return $this->hasOne(Nota::class, 'IdInscripcion', 'IdInscripcion');
    }
}
