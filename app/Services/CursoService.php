<?php

namespace App\Services;

use App\Models\Curso;
use App\Repositories\CursoRepository;
use RuntimeException;
use Throwable;

class CursoService
{
    public function __construct(
        private readonly CursoRepository $cursoRepository
    ) {
    }

    public function index(array $filters = [], int $perPage = 10): array
    {
        try {
            $cursos = $this->cursoRepository->paginateWithFilters($filters, $perPage);

            return [
                'status' => true,
                'message' => 'Listado de cursos cargado correctamente.',
                'data' => [
                    'cursos' => [
                        'data' => $cursos->getCollection()->map(fn (Curso $curso) => $this->formatCurso($curso))->values(),
                        'current_page' => $cursos->currentPage(),
                        'last_page' => $cursos->lastPage(),
                        'per_page' => $cursos->perPage(),
                        'total' => $cursos->total(),
                        'from' => $cursos->firstItem(),
                        'to' => $cursos->lastItem(),
                    ],
                    'filters' => [
                        'search' => $filters['search'] ?? '',
                        'Estado' => $filters['Estado'] ?? '',
                    ],
                ],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudo cargar el listado de cursos.');
        }
    }

    public function store(array $payload): array
    {
        try {
            $curso = $this->cursoRepository->create([
                'Nombre' => $payload['Nombre'],
                'Descripcion' => $payload['Descripcion'] ?? null,
                'Estado' => $payload['Estado'] ?? true,
            ]);

            return [
                'status' => true,
                'message' => 'Curso creado correctamente.',
                'data' => [
                    'curso' => $this->formatCurso($curso),
                ],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudo crear el curso.');
        }
    }

    public function update(Curso $curso, array $payload): array
    {
        try {
            $curso = $this->cursoRepository->update($curso, [
                'Nombre' => $payload['Nombre'],
                'Descripcion' => $payload['Descripcion'] ?? null,
                'Estado' => $payload['Estado'] ?? true,
            ]);

            return [
                'status' => true,
                'message' => 'Curso actualizado correctamente.',
                'data' => [
                    'curso' => $this->formatCurso($curso),
                ],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudo actualizar el curso.');
        }
    }

    public function delete(Curso $curso): array
    {
        try {
            if ($this->cursoRepository->hasDependencies($curso)) {
                return [
                    'status' => false,
                    'message' => 'No se puede eliminar el curso porque ya está asociado a materias.',
                    'data' => [],
                ];
            }

            $this->cursoRepository->delete($curso);

            return [
                'status' => true,
                'message' => 'Curso eliminado correctamente.',
                'data' => [],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudo eliminar el curso.');
        }
    }

    private function formatCurso(Curso $curso): array
    {
        return [
            'IdCurso' => $curso->IdCurso,
            'Nombre' => $curso->Nombre ?: ('Curso '.$curso->IdCurso),
            'Descripcion' => $curso->Descripcion,
            'FechaRegistro' => optional($curso->FechaRegistro)->toISOString(),
            'Estado' => $curso->Estado,
        ];
    }
}
