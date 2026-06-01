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

    public function updateProfile(User $usuario, array $payload): array
    {
        try {
            $this->usuarioRepository->update($usuario->IdUsuario, [
                'Nombre1' => $payload['Nombre1'],
                'Nombre2' => $payload['Nombre2'] ?? null,
                'Apellido1' => $payload['Apellido1'],
                'Apellido2' => $payload['Apellido2'] ?? null,
                'CI' => $payload['CI'],
                'Telefono' => $payload['Telefono'],
                'Correo' => $payload['Correo'],
            ]);

            $updatedUser = $this->usuarioRepository->findById($usuario->IdUsuario);

            return [
                'status' => true,
                'message' => 'Perfil actualizado correctamente.',
                'data' => [
                    'user' => $this->formatUser($updatedUser),
                ],
            ];
        } catch (Throwable $e) {
            report($e);
            throw new RuntimeException('No se pudo actualizar el perfil.');
        }
    }

    public function changePassword(User $usuario, string $currentPassword, string $newPassword): array
    {
        try {
            $dbUser = $this->usuarioRepository->findById($usuario->IdUsuario);

            if (! Hash::check($currentPassword, $dbUser->Contrasena)) {
                return [
                    'status' => false,
                    'message' => 'La contraseña actual es incorrecta.',
                    'data' => [],
                ];
            }

            $this->usuarioRepository->update($usuario->IdUsuario, [
                'Contrasena' => Hash::make($newPassword),
            ]);

            return [
                'status' => true,
                'message' => 'Contraseña cambiada correctamente.',
                'data' => [],
            ];
        } catch (Throwable $e) {
            report($e);
            throw new RuntimeException('No se pudo cambiar la contraseña.');
        }
    }

    private function formatUser(User $usuario): array
    {
        $data = [
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

        if ((int) $usuario->IdRol === 3) {
            $estudianteCarrera = \Illuminate\Support\Facades\DB::table('EstudianteCarrera')
                ->leftJoin('carreras', 'EstudianteCarrera.IdCarrera', '=', 'carreras.IdCarrera')
                ->leftJoin('modalidad', 'EstudianteCarrera.IdModalidad', '=', 'modalidad.IdModalidad')
                ->where('EstudianteCarrera.IdUsuario', $usuario->IdUsuario)
                ->select(
                    'EstudianteCarrera.IdCarrera',
                    'EstudianteCarrera.IdModalidad',
                    'carreras.Nombre as CarreraNombre',
                    'modalidad.Nombre as ModalidadNombre'
                )
                ->first();

            if ($estudianteCarrera) {
                $data['IdCarrera'] = $estudianteCarrera->IdCarrera;
                $data['IdModalidad'] = $estudianteCarrera->IdModalidad;
                $data['CarreraNombre'] = $estudianteCarrera->CarreraNombre;
                $data['ModalidadNombre'] = $estudianteCarrera->ModalidadNombre;
            }
        }

        return $data;
    }
}
