<?php

namespace App\Http\Requests\Nota;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'IdInscripcion' => ['required', 'integer', 'exists:inscripciones,IdInscripcion'],
            'Nota' => ['required', 'numeric', 'between:0,100'],
        ];
    }

    public function messages(): array
    {
        return [
            'IdInscripcion.required' => 'Debe seleccionar una inscripción.',
            'IdInscripcion.exists' => 'La inscripción seleccionada no es válida.',
            'Nota.required' => 'Debe ingresar una nota.',
            'Nota.numeric' => 'La nota debe ser un valor numérico.',
            'Nota.between' => 'La nota debe estar entre 0 y 100.',
        ];
    }
}
