<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UsuarioRepository;
use Illuminate\Support\Facades\Hash;
use Throwable;
use RuntimeException;

class AuthService
{
    public function __construct(
        private readonly UsuarioRepository $usuarioRepository
    ) {
    }

    public function login(string $correo, string $contrasena): array
    {
        try {
            $usuario = $this->usuarioRepository->findActiveByCorreo($correo);

            if (! $usuario) {
                return [
                    'status' => false,
                    'message' => 'Credenciales inválidas o usuario inactivo.',
                    'data' => [],
                ];
            }

            if (! Hash::check($contrasena, $usuario->Contrasena)) {
                return [
                    'status' => false,
                    'message' => 'Credenciales inválidas o usuario inactivo.',
                    'data' => [],
                ];
            }

            $token = $usuario->createToken('auth_token', ['*'])->plainTextToken;

            return [
                'status' => true,
                'message' => 'Inicio de sesión exitoso.',
                'data' => [
                    'user' => $this->formatUser($usuario),
                    'token' => $token,
                ],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudo completar el inicio de sesión.');
        }
    }

    public function logout(User $usuario): array
    {
        try {
            $usuario->currentAccessToken()?->delete();

            return [
                'status' => true,
                'message' => 'Cierre de sesión exitoso.',
                'data' => [],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudo cerrar la sesión.');
        }
    }

    public function profile(User $usuario): array
    {
        try {
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
