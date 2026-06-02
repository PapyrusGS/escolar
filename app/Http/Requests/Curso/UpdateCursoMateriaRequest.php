<?php

namespace App\Http\Requests\Curso;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UpdateCursoMateriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $idCursoMateria = $this->route('id');

        return [
            'IdCurso' => ['required', 'integer', Rule::exists('cursos', 'IdCurso')],
            'IdMateria' => ['required', 'integer', Rule::exists('materias', 'IdMateria')],
            'IdDocente' => ['required', 'integer', Rule::exists('usuarios', 'IdUsuario')->where('IdRol', 2), function ($attribute, $value, Closure $fail) use ($idCursoMateria) {
                $idTurno = $this->input('IdTurno');
                if (!$idTurno) return;

                $choque = DB::table('cursos_materias')
                    ->where('IdDocente', $value)
                    ->where('IdTurno', $idTurno)
                    ->where('Estado', true)
                    ->where('IdCursoMateria', '!=', $idCursoMateria)
                    ->exists();

                if ($choque) {
                    $fail('El docente ya tiene otro curso programado en este turno. Existe un conflicto de horarios.');
                }
            }],
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
