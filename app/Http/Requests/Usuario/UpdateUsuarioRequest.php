<?php

namespace App\Http\Requests\Usuario;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('CI')) {
            $this->merge([
                'CI' => (string) $this->input('CI'),
            ]);
        }
        if ($this->has('Telefono')) {
            $this->merge([
                'Telefono' => (string) $this->input('Telefono'),
            ]);
        }
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('usuario') ?? $this->input('IdUsuario');
        if (is_object($id)) {
            $id = $id->IdUsuario ?? $id->id ?? null;
        }
        if ($id) {
            $id = (int) $id;
        }
        $isStudent = (int) $this->input('IdRol') === 3;

        return [
            'IdRol' => ['required', 'integer', Rule::exists('roles', 'IdRol')],
            'Nombre1' => ['required', 'string', 'max:50'],
            'Nombre2' => ['nullable', 'string', 'max:50'],
            'Apellido1' => ['required', 'string', 'max:50'],
            'Apellido2' => ['nullable', 'string', 'max:50'],
            'CI' => ['required', 'max:20', Rule::unique('usuarios', 'CI')->ignore($id, 'IdUsuario')],
            'Telefono' => ['required', 'max:20'],
            'Correo' => ['required', 'email', 'max:100', Rule::unique('usuarios', 'Correo')->ignore($id, 'IdUsuario')],
            'CorreoPersonal' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('usuarios', 'CorreoPersonal')->ignore($id, 'IdUsuario'),
            ],
            'Contrasena' => ['nullable', 'string', 'min:6', 'confirmed'],
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

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        \Illuminate\Support\Facades\Log::error('Errores de validación para la edición de usuario ID ' . $this->route('id') . ':', $validator->errors()->toArray());
        parent::failedValidation($validator);
    }
}
