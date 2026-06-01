<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class EstudianteCarreraRepository
{
    private string $table = 'estudiantecarrera';

    public function create(int $userId, int $carreraId, int $modalidadId): void
    {
        if (! Schema::hasTable($this->table)) {
            throw new RuntimeException('No se encontró la tabla estudiantecarrera.');
        }

        $columns = Schema::getColumnListing($this->table);

        $data = [];

        $userColumn = $this->findColumn($columns, ['IdUsuario', 'idusuario', 'id_usuario']);
        $carreraColumn = $this->findColumn($columns, ['IdCarrera', 'idcarrera', 'id_carrera']);
        $modalidadColumn = $this->findColumn($columns, ['IdModalidad', 'idmodalidad', 'id_modalidad']);

        if (! $userColumn || ! $carreraColumn || ! $modalidadColumn) {
            throw new RuntimeException(
                'La tabla estudiantecarrera debe tener IdUsuario, IdCarrera e IdModalidad para registrar la relación.'
            );
        }

        $data[$userColumn] = $userId;
        $data[$carreraColumn] = $carreraId;
        $data[$modalidadColumn] = $modalidadId;

        $estadoColumn = $this->findColumn($columns, ['Estado', 'estado']);
        if ($estadoColumn) {
            $data[$estadoColumn] = true;
        }

        $fechaColumn = $this->findColumn($columns, ['FechaRegistro', 'fecha_registro', 'fecharegistro']);
        if ($fechaColumn) {
            $data[$fechaColumn] = now();
        }

        DB::table($this->table)->insert($data);
    }

    private function findColumn(array $columns, array $candidates): ?string
    {
        $normalized = [];

        foreach ($columns as $column) {
            $normalized[strtolower($column)] = $column;
        }

        foreach ($candidates as $candidate) {
            $key = strtolower($candidate);

            if (isset($normalized[$key])) {
                return $normalized[$key];
            }
        }

        return null;
    }

    public function updateOrCreate(int $userId, int $carreraId, int $modalidadId): void
    {
        if (! Schema::hasTable($this->table)) {
            throw new RuntimeException('No se encontró la tabla estudiantecarrera.');
        }

        $columns = Schema::getColumnListing($this->table);
        $userColumn = $this->findColumn($columns, ['IdUsuario', 'idusuario', 'id_usuario']);
        $carreraColumn = $this->findColumn($columns, ['IdCarrera', 'idcarrera', 'id_carrera']);
        $modalidadColumn = $this->findColumn($columns, ['IdModalidad', 'idmodalidad', 'id_modalidad']);

        if (! $userColumn || ! $carreraColumn || ! $modalidadColumn) {
            throw new RuntimeException(
                'La tabla estudiantecarrera debe tener IdUsuario, IdCarrera e IdModalidad para registrar la relación.'
            );
        }

        $exists = DB::table($this->table)->where($userColumn, $userId)->exists();

        $data = [
            $carreraColumn => $carreraId,
            $modalidadColumn => $modalidadId,
        ];

        $estadoColumn = $this->findColumn($columns, ['Estado', 'estado']);
        if ($estadoColumn) {
            $data[$estadoColumn] = true;
        }

        if ($exists) {
            DB::table($this->table)->where($userColumn, $userId)->update($data);
        } else {
            $data[$userColumn] = $userId;
            $fechaColumn = $this->findColumn($columns, ['FechaRegistro', 'fecha_registro', 'fecharegistro']);
            if ($fechaColumn) {
                $data[$fechaColumn] = now();
            }
            DB::table($this->table)->insert($data);
        }
    }

    public function deleteByUsuarioId(int $userId): void
    {
        if (Schema::hasTable($this->table)) {
            $columns = Schema::getColumnListing($this->table);
            $userColumn = $this->findColumn($columns, ['IdUsuario', 'idusuario', 'id_usuario']);
            if ($userColumn) {
                DB::table($this->table)->where($userColumn, $userId)->delete();
            }
        }
    }
}
