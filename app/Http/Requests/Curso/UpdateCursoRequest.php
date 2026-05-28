<?php

namespace App\Http\Requests\Curso;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCursoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'Nombre' => is_string($this->input('Nombre')) ? trim($this->input('Nombre')) : $this->input('Nombre'),
            'Descripcion' => is_string($this->input('Descripcion')) ? trim($this->input('Descripcion')) : $this->input('Descripcion'),
        ]);
    }

    public function rules(): array
    {
        return [
            'Nombre' => ['required', 'string', 'max:100'],
            'Descripcion' => ['nullable', 'string', 'max:255'],
            'Estado' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'Nombre.required' => 'El nombre del curso es obligatorio.',
            'Nombre.max' => 'El nombre del curso no debe superar 100 caracteres.',
            'Estado.required' => 'Debes indicar el estado del curso.',
        ];
    }
}
