<?php

namespace App\Http\Requests\Perfil;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePerfilRequest extends FormRequest
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
        ]);
    }

    public function rules(): array
    {
        $usuario = $this->user();
        $usuarioId = $usuario?->IdUsuario;

        return [
            'Nombre1' => ['required', 'string', 'max:50'],
            'Nombre2' => ['nullable', 'string', 'max:50'],
            'Apellido1' => ['required', 'string', 'max:50'],
            'Apellido2' => ['nullable', 'string', 'max:50'],
            'Telefono' => ['nullable', 'integer', 'digits_between:7,8'],
            'Correo' => ['required', 'email', 'max:100', Rule::unique('usuarios', 'Correo')->ignore($usuarioId, 'IdUsuario')],
        ];
    }

    public function messages(): array
    {
        return [
            'Nombre1.required' => 'El primer nombre es obligatorio.',
            'Apellido1.required' => 'El primer apellido es obligatorio.',
            'Telefono.integer' => 'El teléfono debe ser numérico.',
            'Correo.required' => 'El correo es obligatorio.',
            'Correo.email' => 'El correo no tiene un formato válido.',
            'Correo.unique' => 'El correo ya está registrado.',
        ];
    }
}
