<?php

namespace App\Strategies;

use Illuminate\Support\Facades\DB;

class AdminReporteStrategy implements ReporteStrategyInterface
{
    public function getDatos(array $params): array
    {
        // Obtener el listado de materias por carrera
        $query = DB::table('carreras')
            ->join('carreraMateriaPensum', 'carreras.IdCarrera', '=', 'carreraMateriaPensum.IdCarrera')
            ->join('materias', 'carreraMateriaPensum.IdMateria', '=', 'materias.IdMateria')
            ->join('pensum', 'carreraMateriaPensum.IdPensum', '=', 'pensum.IdPensum')
            ->select(
                'carreras.IdCarrera',
                'carreras.Nombre as Carrera',
                'materias.CodigoMateria',
                'materias.Nombre as Materia',
                'pensum.Nombre as Pensum',
                'carreraMateriaPensum.Semestre'
            )
            ->where('carreras.Estado', true)
            ->where('materias.Estado', true)
            ->where('carreraMateriaPensum.Estado', true);

        // Si se envía IdCarrera específico en los parámetros, filtramos
        if (!empty($params['IdCarrera'])) {
            $query->where('carreras.IdCarrera', $params['IdCarrera']);
        }

        return $query->orderBy('carreras.Nombre')
            ->orderBy('carreraMateriaPensum.Semestre')
            ->orderBy('materias.Nombre')
            ->get()
            ->toArray();
    }

    public function getTitulo(): string
    {
        return 'Listado de Materias por Carrera (Reporte de Administración)';
    }
}
