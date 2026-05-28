<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nota extends Model
{
    use HasFactory;

    protected $table = 'notas';

    protected $primaryKey = 'IdNota';

    public $timestamps = false;

    protected $fillable = [
        'IdEstudiante',
        'IdCursoMateria',
        'Nota',
        'FechaRegistro',
        'Estado',
    ];

    protected function casts(): array
    {
        return [
            'Nota' => 'decimal:2',
            'FechaRegistro' => 'datetime',
            'Estado' => 'boolean',
        ];
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
