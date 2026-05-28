<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    protected function casts(): array
    {
        return [
            'Fecha' => 'datetime',
            'Estado' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'IdInscripcion';
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'IdEstudiante', 'IdUsuario');
    }

    public function cursoMateria(): BelongsTo
    {
        return $this->belongsTo(CursoMateria::class, 'IdCursoMateria', 'IdCursoMateria');
    }
}
