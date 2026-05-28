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
            return [
                'status' => true,
                'message' => 'Datos del formulario cargados correctamente.',
                'data' => [
                    'roles' => $this->rolRepository->activeAll()->map(fn ($rol) => [
                        'IdRol' => $rol->IdRol,
                        'Nombre' => $rol->Nombre,
                        'Descripcion' => $rol->Descripcion,
                    ])->values(),
                ],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudieron cargar los datos del formulario.');
        }
    }

    public function index(array $filters = [], int $perPage = 10): array
    {
        try {
            $usuarios = $this->usuarioRepository->paginateWithFilters($filters, $perPage);

            return [
                'status' => true,
                'message' => 'Listado de usuarios cargado correctamente.',
                'data' => [
                    'roles' => $this->rolRepository->activeAll()->map(fn ($rol) => [
                        'IdRol' => $rol->IdRol,
                        'Nombre' => $rol->Nombre,
                        'Descripcion' => $rol->Descripcion,
                    ])->values(),
                    'usuarios' => [
                        'data' => $usuarios->getCollection()->map(fn (User $usuario) => $this->formatUser($usuario))->values(),
                        'current_page' => $usuarios->currentPage(),
                        'last_page' => $usuarios->lastPage(),
                        'per_page' => $usuarios->perPage(),
                        'total' => $usuarios->total(),
                        'from' => $usuarios->firstItem(),
                        'to' => $usuarios->lastItem(),
                    ],
                    'filters' => [
                        'search' => $filters['search'] ?? '',
                        'IdRol' => $filters['IdRol'] ?? '',
                        'Estado' => $filters['Estado'] ?? '',
                    ],
                ],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudo cargar el listado de usuarios.');
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
                'IdCarrera' => $payload['IdCarrera'] ?? null,
                'Semestre' => $payload['Semestre'] ?? null,
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
                        'IdCarrera' => $usuario->IdCarrera,
                        'Semestre' => $usuario->Semestre,
                        'Estado' => $usuario->Estado,
                    ],
                ],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudo registrar el usuario.');
        }
    }

    public function update(User $usuario, array $payload): array
    {
        try {
            $data = [
                'IdRol' => $payload['IdRol'],
                'Nombre1' => $payload['Nombre1'],
                'Nombre2' => $payload['Nombre2'] ?? null,
                'Apellido1' => $payload['Apellido1'],
                'Apellido2' => $payload['Apellido2'] ?? null,
                'CI' => $payload['CI'],
                'Telefono' => $payload['Telefono'] ?? null,
                'Correo' => $payload['Correo'],
                'IdCarrera' => $payload['IdCarrera'] ?? null,
                'Semestre' => $payload['Semestre'] ?? null,
                'Estado' => $payload['Estado'] ?? true,
            ];

            if (! empty($payload['Contrasena'])) {
                $data['Contrasena'] = Hash::make($payload['Contrasena']);
            }

            $usuario = $this->usuarioRepository->update($usuario, $data);

            return [
                'status' => true,
                'message' => 'Usuario actualizado correctamente.',
                'data' => [
                    'user' => $this->formatUser($usuario),
                ],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudo actualizar el usuario.');
        }
    }

    public function toggleEstado(User $usuario): array
    {
        try {
            $usuario = $this->usuarioRepository->toggleEstado($usuario);

            return [
                'status' => true,
                'message' => $usuario->Estado ? 'Usuario activado correctamente.' : 'Usuario desactivado correctamente.',
                'data' => [
                    'user' => $this->formatUser($usuario),
                ],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudo cambiar el estado del usuario.');
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
