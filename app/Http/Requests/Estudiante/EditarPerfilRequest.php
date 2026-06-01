<?php

namespace App\Http\Requests\Estudiante;

use Illuminate\Foundation\Http\FormRequest;

class EditarPerfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cualquiera autenticado puede editar su perfil
    }

    public function rules(): array
    {
        return [
            'Telefono'  => 'required|string|max:20',
            'Nombre1'   => 'required|string|max:50',
            'Nombre2'   => 'nullable|string|max:50',
            'Apellido1' => 'required|string|max:50',
            'Apellido2' => 'nullable|string|max:50',
        ];
    }
}