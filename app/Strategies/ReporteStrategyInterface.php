<?php

namespace App\Strategies;

interface ReporteStrategyInterface
{
    public function getDatos(string $tipoReporte, array $params): array;

    public function getTitulo(string $tipoReporte): string;

    public function getTiposReportes(): array;

    public function getFiltros(string $tipoReporte): array;
}
