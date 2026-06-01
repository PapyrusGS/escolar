<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CarreraRepository
{
    public function activeAll(): Collection
    {
        return DB::table('carreras')
            ->where('Estado', true)
            ->orderBy('IdCarrera')
            ->get();
    }
}
