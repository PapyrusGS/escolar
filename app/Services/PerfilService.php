<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UsuarioRepository;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Throwable;

class PerfilService
{
    public function __construct(
        private readonly UsuarioRepository $usuarioRepository
    ) {
    }

    public function show(User $usuario): array
    {
        try {
            $usuario = $this->usuarioRepository->findById($usuario->IdUsuario) ?? $usuario->load('rol');

            return [
                'status' => true,
                'message' => 'Perfil recuperado correctamente.',
                'data' => [
                    'user' => $this->formatUser($usuario),
                ],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudo recuperar el perfil.');
        }
    }

    public function update(User $usuario, array $payload): array
    {
        try {
            $usuario = $this->usuarioRepository->update($usuario, [
                'Nombre1' => $payload['Nombre1'],
                'Nombre2' => $payload['Nombre2'] ?? null,
                'Apellido1' => $payload['Apellido1'],
                'Apellido2' => $payload['Apellido2'] ?? null,
                'Telefono' => $payload['Telefono'] ?? null,
                'Correo' => $payload['Correo'],
            ]);

            return [
                'status' => true,
                'message' => 'Perfil actualizado correctamente.',
                'data' => [
                    'user' => $this->formatUser($usuario),
                ],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudo actualizar el perfil.');
        }
    }

    public function changePassword(User $usuario, string $currentPassword, string $password): array
    {
        try {
            if (! Hash::check($currentPassword, $usuario->Contrasena)) {
                return [
                    'status' => false,
                    'message' => 'La contraseña actual no es correcta.',
                    'data' => [],
                ];
            }

            $this->usuarioRepository->update($usuario, [
                'Contrasena' => Hash::make($password),
            ]);

            return [
                'status' => true,
                'message' => 'Contraseña actualizada correctamente.',
                'data' => [],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudo cambiar la contraseña.');
        }
    }

    private function formatUser(User $usuario): array
    {
        return [
            'IdUsuario' => $usuario->IdUsuario,
            'IdRol' => $usuario->IdRol,
            'Rol' => [
                'IdRol' => $usuario->rol?->IdRol,
                'Nombre' => $usuario->rol?->Nombre,
                'Descripcion' => $usuario->rol?->Descripcion,
            ],
            'Nombre1' => $usuario->Nombre1,
            'Nombre2' => $usuario->Nombre2,
            'Apellido1' => $usuario->Apellido1,
            'Apellido2' => $usuario->Apellido2,
            'CI' => $usuario->CI,
            'Telefono' => $usuario->Telefono,
            'Correo' => $usuario->Correo,
            'FechaRegistro' => optional($usuario->FechaRegistro)->toISOString(),
            'IdCarrera' => $usuario->IdCarrera,
            'Semestre' => $usuario->Semestre,
            'Estado' => $usuario->Estado,
        ];
    }
}
