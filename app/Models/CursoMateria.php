<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CursoMateria extends Model
{
    use HasFactory;

    protected $table = 'cursos_materias';

    protected $primaryKey = 'IdCursoMateria';

    public $timestamps = false;

    protected $fillable = [
        'IdCurso',
        'IdMateria',
        'IdDocente',
        'IdTurno',
        'FechaInicio',
        'FechaFin',
        'MaxInscritos',
        'Inscritos',
        'FechaRegistro',
        'Estado',
    ];

    protected function casts(): array
    {
        return [
            'FechaInicio' => 'datetime',
            'FechaFin' => 'datetime',
            'FechaRegistro' => 'datetime',
            'Estado' => 'boolean',
            'MaxInscritos' => 'integer',
            'Inscritos' => 'integer',
        ];
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'IdCurso', 'IdCurso');
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'IdMateria', 'IdMateria');
    }

    public function docente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'IdDocente', 'IdUsuario');
    }

    public function turno(): BelongsTo
    {
        return $this->belongsTo(Turno::class, 'IdTurno', 'IdTurno');
    }
}
