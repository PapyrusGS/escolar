<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'Correo' => is_string($this->input('Correo')) ? trim($this->input('Correo')) : $this->input('Correo'),
        ]);
    }

    public function rules(): array
    {
        return [
            'Correo' => ['required', 'string', 'email', 'exists:usuarios,Correo'],
            'Contrasena' => ['required', 'string', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'Correo.required' => 'El campo Correo es obligatorio.',
            'Correo.email' => 'El formato del Correo no es válido.',
            'Correo.exists' => 'El Correo no está registrado.',
            'Contrasena.required' => 'El campo Contrasena es obligatorio.',
            'Contrasena.min' => 'La Contrasena debe tener al menos 6 caracteres.',
        ];
    }
}
