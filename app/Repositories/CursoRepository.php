<?php

namespace App\Repositories;

use App\Models\Curso;

class CursoRepository
{
    public function activeAll(): iterable
    {
        return Curso::query()
            ->where('Estado', true)
            ->get();
    }
}
