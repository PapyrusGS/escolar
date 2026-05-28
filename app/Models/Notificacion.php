<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    protected $primaryKey = 'IdNotificacion';

    public $timestamps = false;

    protected $fillable = [
        'IdUsuario',
        'Titulo',
        'Contenido',
        'FechaEnvio',
        'FechaRegistro',
        'Estado',
    ];

    protected function casts(): array
    {
        return [
            'FechaEnvio' => 'datetime',
            'FechaRegistro' => 'datetime',
            'Estado' => 'boolean',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'IdUsuario', 'IdUsuario');
    }
}
