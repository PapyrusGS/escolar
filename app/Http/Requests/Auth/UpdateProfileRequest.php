<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
        $id = $this->user()?->IdUsuario;

        return [
            'Nombre1' => ['required', 'string', 'max:50'],
            'Nombre2' => ['nullable', 'string', 'max:50'],
            'Apellido1' => ['required', 'string', 'max:50'],
            'Apellido2' => ['nullable', 'string', 'max:50'],
            'CI' => ['required', 'max:20', Rule::unique('usuarios', 'CI')->ignore($id, 'IdUsuario')],
            'Telefono' => ['required', 'max:20'],
            'Correo' => ['required', 'email', 'max:100', Rule::unique('usuarios', 'Correo')->ignore($id, 'IdUsuario')],
        ];
    }
}
