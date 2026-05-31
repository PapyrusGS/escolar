<?php

namespace App\Repositories;

use App\Models\Rol;
use Illuminate\Support\Collection;

class RolRepository
{
    public function activeAll(): Collection
    {
        return Rol::query()
            ->where('Estado', true)
            ->orderBy('IdRol')
            ->get();
    }
}
