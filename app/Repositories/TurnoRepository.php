<?php

namespace App\Repositories;

use App\Models\Turno;

class TurnoRepository
{
    public function activeAll(): iterable
    {
        return Turno::query()
            ->where('Estado', true)
            ->get();
    }
}
