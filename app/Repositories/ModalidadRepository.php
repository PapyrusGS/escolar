<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ModalidadRepository
{
    public function activeAll(): Collection
    {
        return DB::table('modalidad')
            ->where('Estado', true)
            ->orderBy('IdModalidad')
            ->get();
    }
}
