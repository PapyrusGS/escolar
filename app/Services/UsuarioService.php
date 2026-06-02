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
                    'CorreoPersonal' => $payload['CorreoPersonal'] ?? null,
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

    public function all(): array
    {
        return collect($this->usuarioRepository->all())->map(function ($user) {
            return [
                'IdUsuario' => $user->IdUsuario,
                'IdRol' => $user->IdRol,
                'Nombre1' => $user->Nombre1,
                'Nombre2' => $user->Nombre2,
                'Apellido1' => $user->Apellido1,
                'Apellido2' => $user->Apellido2,
                'CI' => $user->CI,
                'Telefono' => $user->Telefono,
                'Correo' => $user->Correo,
                'CorreoPersonal' => $user->CorreoPersonal,
                'FechaRegistro' => $user->FechaRegistro,
                'Estado' => (bool)$user->Estado,
                'Rol' => $user->rol,
                'IdCarrera' => $user->IdCarrera,
                'IdModalidad' => $user->IdModalidad,
            ];
        })->all();
    }

    public function update(int $id, array $payload): array
    {
        try {
            return DB::transaction(function () use ($id, $payload) {
                $usuario = $this->usuarioRepository->findById($id);
                if (!$usuario) {
                    throw new RuntimeException('Usuario no encontrado.');
                }

                $data = [
                    'IdRol' => (int) $usuario->IdRol,
                    'Nombre1' => $payload['Nombre1'],
                    'Nombre2' => $payload['Nombre2'] ?? null,
                    'Apellido1' => $payload['Apellido1'],
                    'Apellido2' => $payload['Apellido2'] ?? null,
                    'CI' => $payload['CI'],
                    'Telefono' => $payload['Telefono'],
                    'Correo' => $payload['Correo'],
                    'CorreoPersonal' => $payload['CorreoPersonal'] ?? null,
                    'Estado' => (bool) ($payload['Estado'] ?? true),
                ];

                if (!empty($payload['Contrasena'])) {
                    $data['Contrasena'] = Hash::make($payload['Contrasena']);
                }

                $this->usuarioRepository->update($id, $data);

                if ((int) $usuario->IdRol === 3) {
                    $this->estudianteCarreraRepository->updateOrCreate(
                        $id,
                        (int) $payload['IdCarrera'],
                        (int) $payload['IdModalidad']
                    );
                }

                return [
                    'user' => $this->usuarioRepository->findById($id),
                ];
            });
        } catch (\Throwable $e) {
            throw new RuntimeException('No se pudo actualizar el usuario: ' . $e->getMessage(), 0, $e);
        }
    }

    public function destroy(int $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $this->estudianteCarreraRepository->deleteByUsuarioId($id);
                return $this->usuarioRepository->delete($id);
            });
        } catch (\Throwable $e) {
            throw new RuntimeException('No se pudo eliminar el usuario debido a dependencias académicas o del sistema: ' . $e->getMessage(), 0, $e);
        }
    }

    public function toggleStatus(int $id): bool
    {
        $usuario = $this->usuarioRepository->findById($id);
        if (!$usuario) {
            throw new RuntimeException('Usuario no encontrado.');
        }
        return $this->usuarioRepository->update($id, [
            'Estado' => !$usuario->Estado
        ]);
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
