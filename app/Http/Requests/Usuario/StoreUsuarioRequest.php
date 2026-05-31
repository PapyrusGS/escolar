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
        return [
            'IdRol' => ['required', 'integer', Rule::exists('roles', 'IdRol')],
            'Nombre1' => ['required', 'string', 'max:50'],
            'Nombre2' => ['nullable', 'string', 'max:50'],
            'Apellido1' => ['required', 'string', 'max:50'],
            'Apellido2' => ['nullable', 'string', 'max:50'],
            'CI' => ['required', 'integer', 'unique:usuarios,CI'],
            'Telefono' => ['nullable', 'integer'],
            'Correo' => ['required', 'email', 'max:100', 'unique:usuarios,Correo'],
            'Contrasena' => ['required', 'string', 'min:6', 'confirmed'],
            'Estado' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'IdRol.required' => 'Debes seleccionar un rol.',
            'Contrasena.confirmed' => 'La confirmacion de la contrasena no coincide.',
        ];
    }
}
