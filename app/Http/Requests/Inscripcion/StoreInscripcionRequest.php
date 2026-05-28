<?php

namespace App\Http\Requests\Inscripcion;

use Illuminate\Foundation\Http\FormRequest;

class StoreInscripcionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'IdCursoMateria' => ['required', 'integer', 'exists:cursos_materias,IdCursoMateria'],
        ];
    }
}
