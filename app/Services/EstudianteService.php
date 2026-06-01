<?php

namespace App\Services;

use App\Repositories\EstudianteRepository;
use Illuminate\Support\Facades\Hash;

class EstudianteService
{
    // Inyección de dependencias del Repositorio (PHP 8.2+ promoted property)
    public function __construct(
        protected readonly EstudianteRepository $estudianteRepo
    ) {}

    /**
     * Lógica para obtener el perfil formateado
     */
    public function obtenerPerfilCompleto(int $idUsuario)
    {
        return $this->estudianteRepo->findUsuarioConRol($idUsuario);
    }

    /**
     * Lógica para actualizar datos comunes
     */
    public function actualizarDatos(int $idUsuario, array $data)
    {
        return $this->estudianteRepo->updateUsuario($idUsuario, $data);
    }

    /**
     * Lógica de seguridad para el cambio de contraseña
     */
    public function modificarContrasena($user, array $data): array
    {
        // Revisamos si la contraseña vieja coincide con la encriptada en la BD
        if (!Hash::check($data['password_actual'], $user->Contrasena)) {
            return [
                'status' => false,
                'message' => 'La contraseña actual es incorrecta.',
                'data' => []
            ];
        }

        // Si es correcta, encriptamos la nueva 'Contrasena' y mandamos a actualizar
        $this->estudianteRepo->updateUsuario($user->IdUsuario, [
            'Contrasena' => Hash::make($data['Contrasena'])
        ]);

        return [
            'status' => true,
            'message' => 'Contraseña cambiada con éxito.',
            'data' => []
        ];
    }
}