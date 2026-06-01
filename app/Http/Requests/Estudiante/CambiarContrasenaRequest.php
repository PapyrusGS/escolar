<?php

namespace App\Http\Requests\Estudiante;

use Illuminate\Foundation\Http\FormRequest;

class CambiarContrasenaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password_actual' => 'required|string',
            'Contrasena'      => 'required|string|min:6', // Apunta directo a tu BD custom
        ];
    }
}