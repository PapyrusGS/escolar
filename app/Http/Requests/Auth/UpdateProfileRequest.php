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
            'Telefono' => ['required', 'string', 'max:20'],
            'CorreoPersonal' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('usuarios', 'CorreoPersonal')->ignore($id, 'IdUsuario'),
            ],
        ];
    }
}
