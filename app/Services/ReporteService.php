<?php

namespace App\Services;

use App\Strategies\ReporteStrategyInterface;
use App\Strategies\AdminReporteStrategy;
use App\Strategies\DocenteReporteStrategy;
use App\Strategies\EstudianteReporteStrategy;
use InvalidArgumentException;

class ReporteService
{
    private array $strategies = [];

    public function __construct()
    {
        $this->strategies = [
            1 => new AdminReporteStrategy(),
            2 => new DocenteReporteStrategy(),
            3 => new EstudianteReporteStrategy(),
        ];
    }

    public function obtenerTiposReportesPorRol(int $idRol): array
    {
        if (!isset($this->strategies[$idRol])) {
            return [];
        }

        return $this->strategies[$idRol]->getTiposReportes();
    }

    public function obtenerFiltrosPorRol(int $idRol, string $tipoReporte): array
    {
        if (!isset($this->strategies[$idRol])) {
            return [];
        }

        return $this->strategies[$idRol]->getFiltros($tipoReporte);
    }

    public function generarReporte(int $idRol, string $tipoReporte, array $params): array
    {
        if (!isset($this->strategies[$idRol])) {
            throw new InvalidArgumentException("No existe estrategia de reporte para el rol: {$idRol}");
        }

        /** @var ReporteStrategyInterface $strategy */
        $strategy = $this->strategies[$idRol];

        $reportesSoportados = $strategy->getTiposReportes();
        if (!isset($reportesSoportados[$tipoReporte])) {
            throw new InvalidArgumentException("El tipo de reporte '{$tipoReporte}' no está soportado para este rol.");
        }

        return [
            'status' => true,
            'message' => 'Reporte generado correctamente',
            'data' => [
                'titulo' => $strategy->getTitulo($tipoReporte),
                'datos' => $strategy->getDatos($tipoReporte, $params)
            ]
        ];
    }
}
