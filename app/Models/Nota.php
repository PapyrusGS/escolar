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
        'IdInscripcion',
        'Nota',
        'FechaRegistro',
        'Estado',
    ];

    protected function casts(): array
    {
        return [
            'FechaRegistro' => 'datetime',
            'Estado' => 'boolean',
            'Nota' => 'decimal:2',
        ];
    }

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class, 'IdInscripcion', 'IdInscripcion');
    }
}
