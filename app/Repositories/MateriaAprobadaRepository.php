<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class MateriaAprobadaRepository
{
    public function getApprovedMateriaIds(int $idUsuario): array
    {
        return DB::table('materias_aprobadas')
            ->where('IdUsuario', $idUsuario)
            ->where('Estado', true)
            ->pluck('IdMateria')
            ->map(fn($v) => (int) $v)
            ->all();
    }

    public function getApprovedCount(int $idUsuario): int
    {
        return DB::table('materias_aprobadas')
            ->where('IdUsuario', $idUsuario)
            ->where('Estado', true)
            ->count();
    }

    public function upsertOnApproval(int $idUsuario, int $idMateria, int $idCarrera, float $notaFinal): void
    {
        DB::table('materias_aprobadas')->updateOrInsert(
            [
                'IdUsuario' => $idUsuario,
                'IdMateria' => $idMateria,
            ],
            [
                'IdCarrera' => $idCarrera,
                'NotaFinal' => $notaFinal,
                'FechaAprobacion' => now()->toDateString(),
                'Estado' => true,
            ]
        );
    }

    public function deleteOnRevocation(int $idUsuario, int $idMateria): void
    {
        DB::table('materias_aprobadas')
            ->where('IdUsuario', $idUsuario)
            ->where('IdMateria', $idMateria)
            ->delete();
    }
}
