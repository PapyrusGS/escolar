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

    protected function prepareForValidation(): void
    {
        $this->merge([
            'Correo' => is_string($this->input('Correo')) ? trim($this->input('Correo')) : $this->input('Correo'),
            'Nombre1' => is_string($this->input('Nombre1')) ? trim($this->input('Nombre1')) : $this->input('Nombre1'),
            'Nombre2' => is_string($this->input('Nombre2')) ? trim($this->input('Nombre2')) : $this->input('Nombre2'),
            'Apellido1' => is_string($this->input('Apellido1')) ? trim($this->input('Apellido1')) : $this->input('Apellido1'),
            'Apellido2' => is_string($this->input('Apellido2')) ? trim($this->input('Apellido2')) : $this->input('Apellido2'),
            'Contrasena' => filled($this->input('Contrasena')) ? $this->input('Contrasena') : null,
            'Contrasena_confirmation' => filled($this->input('Contrasena_confirmation')) ? $this->input('Contrasena_confirmation') : null,
        ]);
    }

    public function rules(): array
    {
        $usuarioId = $this->route('usuario')?->IdUsuario ?? $this->route('usuario');

        return [
            'IdRol' => ['required', 'integer', Rule::exists('roles', 'IdRol')->where('Estado', true)],
            'Nombre1' => ['required', 'string', 'max:50'],
            'Nombre2' => ['nullable', 'string', 'max:50'],
            'Apellido1' => ['required', 'string', 'max:50'],
            'Apellido2' => ['nullable', 'string', 'max:50'],
            'CI' => ['required', 'integer', 'digits_between:6,10', Rule::unique('usuarios', 'CI')->ignore($usuarioId, 'IdUsuario')],
            'Telefono' => ['nullable', 'integer', 'digits_between:7,8'],
            'Correo' => ['required', 'email', 'max:100', Rule::unique('usuarios', 'Correo')->ignore($usuarioId, 'IdUsuario')],
            'Contrasena' => ['nullable', 'string', 'min:6', 'confirmed'],
            'IdCarrera' => ['nullable', 'integer', 'exists:carreras,IdCarrera'],
            'Semestre' => ['nullable', 'integer', 'min:1', 'max:12'],
            'Estado' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'IdRol.required' => 'Debes seleccionar un rol.',
            'IdRol.exists' => 'El rol seleccionado no es válido o está inactivo.',
            'Nombre1.required' => 'El primer nombre es obligatorio.',
            'Apellido1.required' => 'El primer apellido es obligatorio.',
            'CI.required' => 'El CI es obligatorio.',
            'CI.unique' => 'El CI ya está registrado.',
            'Correo.required' => 'El correo es obligatorio.',
            'Correo.email' => 'El correo no tiene un formato válido.',
            'Correo.unique' => 'El correo ya está registrado.',
            'Contrasena.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'Contrasena.confirmed' => 'La confirmación de contraseña no coincide.',
            'Estado.required' => 'Debes indicar si el usuario estará activo o inactivo.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ((int) $this->input('IdRol') === 3) {
                if (! $this->filled('IdCarrera')) {
                    $validator->errors()->add('IdCarrera', 'Para un estudiante, la carrera es obligatoria.');
                }

                if (! $this->filled('Semestre')) {
                    $validator->errors()->add('Semestre', 'Para un estudiante, el semestre es obligatorio.');
                }
            }
        });
    }
}
