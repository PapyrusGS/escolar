<?php

namespace App\Http\Requests\Curso;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCursoMateriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'IdCurso' => ['required', 'integer', Rule::exists('cursos', 'IdCurso')],
            'IdMateria' => ['required', 'integer', Rule::exists('materias', 'IdMateria')],
            'IdDocente' => ['required', 'integer', Rule::exists('usuarios', 'IdUsuario')->where('IdRol', 2)],
            'IdTurno' => ['required', 'integer', Rule::exists('turnos', 'IdTurno')],
            'FechaInicio' => ['required', 'date'],
            'FechaFin' => ['required', 'date', 'after_or_equal:FechaInicio'],
            'MaxInscritos' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'IdCurso.exists' => 'El aula física seleccionada no es válida.',
            'IdMateria.exists' => 'La materia seleccionada no es válida.',
            'IdDocente.exists' => 'El docente seleccionado no es válido.',
            'IdTurno.exists' => 'El turno seleccionado no es válido.',
            'FechaFin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
        ];
    }
}
