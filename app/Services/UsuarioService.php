<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\RolRepository;
use App\Repositories\UsuarioRepository;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Throwable;

class UsuarioService
{
    public function __construct(
        private readonly UsuarioRepository $usuarioRepository,
        private readonly RolRepository $rolRepository
    ) {
    }

    public function formData(): array
    {
        try {
            $roles = $this->rolRepository->activeAll();

            return [
                'status' => true,
                'message' => 'Datos del formulario cargados correctamente.',
                'data' => [
                    'roles' => $roles->map(fn ($rol) => [
                        'IdRol' => $rol->IdRol,
                        'Nombre' => $rol->Nombre,
                        'Descripcion' => $rol->Descripcion,
                    ])->values(),
                ],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudieron cargar los datos del formulario: '.$e->getMessage(), 0, $e);
        }
    }

    public function store(array $payload): array
    {
        try {
            $usuario = $this->usuarioRepository->create([
                'IdRol' => $payload['IdRol'],
                'Nombre1' => $payload['Nombre1'],
                'Nombre2' => $payload['Nombre2'] ?? null,
                'Apellido1' => $payload['Apellido1'],
                'Apellido2' => $payload['Apellido2'] ?? null,
                'CI' => $payload['CI'],
                'Telefono' => $payload['Telefono'] ?? null,
                'Correo' => $payload['Correo'],
                'Contrasena' => Hash::make($payload['Contrasena']),
                'Estado' => $payload['Estado'] ?? true,
            ]);

            $usuario->load('rol');

            return [
                'status' => true,
                'message' => 'Usuario registrado correctamente.',
                'data' => [
                    'user' => [
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
                        'Estado' => $usuario->Estado,
                    ],
                ],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudo registrar el usuario: '.$e->getMessage(), 0, $e);
        }
    }
}
