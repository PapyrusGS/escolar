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
        // Registrar estrategias según el rol o clave
        $this->strategies = [
            1 => new AdminReporteStrategy(),
            2 => new DocenteReporteStrategy(),
            3 => new EstudianteReporteStrategy(),
        ];
    }

    public function generarReporte(int $idRol, array $params): array
    {
        if (!isset($this->strategies[$idRol])) {
            throw new InvalidArgumentException("No existe estrategia de reporte para el rol: {$idRol}");
        }

        /** @var ReporteStrategyInterface $strategy */
        $strategy = $this->strategies[$idRol];

        return [
            'status' => true,
            'message' => 'Reporte generado correctamente con patrón Strategy',
            'data' => [
                'titulo' => $strategy->getTitulo(),
                'datos' => $strategy->getDatos($params)
            ]
        ];
    }
}
