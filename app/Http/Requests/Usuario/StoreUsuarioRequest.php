<?php

namespace App\Http\Requests\Usuario;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isStudent = (int) $this->input('IdRol') === 3;

        return [
            'IdRol' => ['required', 'integer', Rule::exists('roles', 'IdRol')],
            'Nombre1' => ['required', 'string', 'max:50'],
            'Nombre2' => ['nullable', 'string', 'max:50'],
            'Apellido1' => ['required', 'string', 'max:50'],
            'Apellido2' => ['nullable', 'string', 'max:50'],
            'CI' => ['required', 'string', 'max:20', Rule::unique('usuarios', 'CI')],
            'Telefono' => ['required', 'string', 'max:20'],
            'Correo' => ['required', 'email', 'max:100', Rule::unique('usuarios', 'Correo')],
            'CorreoPersonal' => ['nullable', 'email', 'max:100', Rule::unique('usuarios', 'CorreoPersonal')],
            'Contrasena' => ['required', 'string', 'min:6', 'confirmed'],
            'IdCarrera' => [
                Rule::requiredIf($isStudent),
                'nullable',
                'integer',
                Rule::exists('carreras', 'IdCarrera'),
            ],
            'IdModalidad' => [
                Rule::requiredIf($isStudent),
                'nullable',
                'integer',
                Rule::exists('modalidad', 'IdModalidad'),
            ],
            'Estado' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'IdCarrera.required' => 'La carrera es obligatoria para estudiantes.',
            'IdModalidad.required' => 'La modalidad es obligatoria para estudiantes.',
            'Contrasena.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }
}
