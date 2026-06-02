<?php

namespace App\Http\Requests\Estudiante;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class InscribirCursoRequest extends FormRequest
{
    /**
     * Determina si el estudiante está autorizado a realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación aplicadas a la solicitud de inscripción.
     */
    public function rules(): array
    {
        return [
            // Validamos que se envíe el ID correcto y que exista en la tabla cursos_materias
            'IdCursoMateria' => 'required|integer|exists:cursos_materias,IdCursoMateria',
        ];
    }

    /**
     * Mensajes de error personalizados en caso de que falle la validación.
     */
    public function messages(): array
    {
        return [
            'IdCursoMateria.required' => 'El campo IdCursoMateria es obligatorio.',
            'IdCursoMateria.integer'  => 'El IdCursoMateria debe ser un número entero.',
            'IdCursoMateria.exists'   => 'La materia seleccionada no es válida o no existe en la oferta académica.',
        ];
    }

    /**
     * Manejo personalizado de fallos de validación para mantener tu estándar de respuestas JSON.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Datos de entrada no válidos.',
            'data' => $validator->errors()
        ], 422));
    }
}