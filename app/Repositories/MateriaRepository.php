<?php

namespace App\Repositories;

use App\Models\Materia;

class MateriaRepository
{
    public function activeAll(): iterable
    {
        return Materia::query()
            ->where('Estado', true)
            ->get();
    }
}
