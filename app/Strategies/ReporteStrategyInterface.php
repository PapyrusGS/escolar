<?php

namespace App\Strategies;

interface ReporteStrategyInterface
{
    public function getDatos(array $params): array;
    public function getTitulo(): string;
}
