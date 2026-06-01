<?php

namespace App\Services;

use App\Repositories\CarreraRepository;
use App\Repositories\EstudianteCarreraRepository;
use App\Repositories\ModalidadRepository;
use App\Repositories\RolRepository;
use App\Repositories\UsuarioRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class UsuarioService
{
    public function __construct(
        private readonly UsuarioRepository $usuarioRepository,
        private readonly RolRepository $rolRepository,
        private readonly CarreraRepository $carreraRepository,
        private readonly EstudianteCarreraRepository $estudianteCarreraRepository,
        private readonly ModalidadRepository $modalidadRepository,
    ) {
    }

    public function formData(): array
    {
        return [
            'roles' => $this->mapRows($this->rolRepository->activeAll(), ['IdRol', 'Nombre']),
            'carreras' => $this->mapRows($this->carreraRepository->activeAll(), ['IdCarrera', 'Nombre']),
            'modalidades' => $this->mapRows($this->modalidadRepository->activeAll(), ['IdModalidad', 'Nombre']),
        ];
    }

    public function store(array $payload): array
    {
        try {
            return DB::transaction(function () use ($payload) {
                $usuario = $this->usuarioRepository->create([
                    'IdRol' => (int) $payload['IdRol'],
                    'Nombre1' => $payload['Nombre1'],
                    'Nombre2' => $payload['Nombre2'] ?? null,
                    'Apellido1' => $payload['Apellido1'],
                    'Apellido2' => $payload['Apellido2'] ?? null,
                    'CI' => $payload['CI'],
                    'Telefono' => $payload['Telefono'],
                    'Correo' => $payload['Correo'],
                    'Contrasena' => Hash::make($payload['Contrasena']),
                    'FechaRegistro' => now(),
                    'Estado' => (bool) ($payload['Estado'] ?? true),
                ]);

                if ((int) $payload['IdRol'] === 3) {
                    $this->estudianteCarreraRepository->create(
                        (int) $usuario->IdUsuario,
                        (int) $payload['IdCarrera'],
                        (int) $payload['IdModalidad']
                    );
                }

                return [
                    'user' => $this->usuarioRepository->findById($usuario->IdUsuario),
                ];
            });
        } catch (\Throwable $e) {
            throw new RuntimeException('No se pudo registrar el usuario: ' . $e->getMessage(), 0, $e);
        }
    }

    private function mapRows(iterable $rows, array $fields): array
    {
        return collect($rows)->map(function ($row) use ($fields) {
            $mapped = [];

            foreach ($fields as $field) {
                $mapped[$field] = $row->{$field} ?? $row->{strtolower($field)} ?? null;
            }

            return $mapped;
        })->all();
    }
}
