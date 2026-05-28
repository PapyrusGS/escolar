<?php

namespace App\Repositories;

use App\Models\CursoMateria;
use App\Models\Inscripcion;
use App\Models\Nota;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Support\Collection;

class DashboardRepository
{
    public function summary(User $user): array
    {
        $roleId = (int) $user->IdRol;

        return match ($roleId) {
            1 => [
                'total_users' => User::query()->count(),
                'total_active_courses' => CursoMateria::query()->where('Estado', true)->count(),
                'total_active_enrollments' => Inscripcion::query()->where('Estado', true)->count(),
                'total_active_grades' => Nota::query()->where('Estado', true)->count(),
            ],
            2 => [
                'assigned_courses' => CursoMateria::query()
                    ->where('IdDocente', $user->IdUsuario)
                    ->where('Estado', true)
                    ->count(),
                'active_enrollments' => Inscripcion::query()
                    ->whereHas('cursoMateria', function ($query) use ($user) {
                        $query->where('IdDocente', $user->IdUsuario);
                    })
                    ->where('Estado', true)
                    ->count(),
                'active_grades' => Nota::query()
                    ->whereHas('cursoMateria', function ($query) use ($user) {
                        $query->where('IdDocente', $user->IdUsuario);
                    })
                    ->where('Estado', true)
                    ->count(),
            ],
            3 => [
                'enrolled_courses' => Inscripcion::query()
                    ->where('IdEstudiante', $user->IdUsuario)
                    ->where('Estado', true)
                    ->count(),
                'average_grade' => (float) (Nota::query()
                    ->where('IdEstudiante', $user->IdUsuario)
                    ->where('Estado', true)
                    ->avg('Nota') ?? 0),
                'active_notifications' => Notificacion::query()
                    ->where('IdUsuario', $user->IdUsuario)
                    ->where('Estado', true)
                    ->count(),
            ],
            default => [
                'total_notifications' => Notificacion::query()
                    ->where('IdUsuario', $user->IdUsuario)
                    ->where('Estado', true)
                    ->count(),
            ],
        };
    }

    public function recentActivities(User $user): Collection
    {
        $roleId = (int) $user->IdRol;

        return match ($roleId) {
            1 => $this->adminActivities(),
            2 => $this->teacherActivities($user),
            3 => $this->studentActivities($user),
            default => collect(),
        };
    }

    public function relevantInfo(User $user): array
    {
        return [
            'user' => [
                'IdUsuario' => $user->IdUsuario,
                'NombreCompleto' => trim(collect([
                    $user->Nombre1,
                    $user->Nombre2,
                    $user->Apellido1,
                    $user->Apellido2,
                ])->filter()->implode(' ')),
                'Correo' => $user->Correo,
                'Rol' => $user->rol?->Nombre,
                'DescripcionRol' => $user->rol?->Descripcion,
                'Carrera' => $user->IdCarrera,
                'Semestre' => $user->Semestre,
                'Estado' => $user->Estado,
            ],
        ];
    }

    private function adminActivities(): Collection
    {
        $recentUsers = User::query()
            ->with('rol')
            ->orderByDesc('FechaRegistro')
            ->limit(4)
            ->get()
            ->map(fn (User $user) => [
                'type' => 'usuario',
                'title' => 'Usuario registrado',
                'description' => trim(($user->Nombre1 ?? '').' '.($user->Apellido1 ?? '')),
                'meta' => optional($user->FechaRegistro)->diffForHumans(),
                'activity_at' => optional($user->FechaRegistro)->timestamp ?? 0,
            ]);

        $recentEnrollments = Inscripcion::query()
            ->with(['estudiante', 'cursoMateria.curso', 'cursoMateria.materia'])
            ->orderByDesc('Fecha')
            ->limit(4)
            ->get()
            ->map(fn (Inscripcion $inscripcion) => [
                'type' => 'inscripcion',
                'title' => 'Nueva inscripcion',
                'description' => trim(($inscripcion->estudiante?->Nombre1 ?? '').' '.($inscripcion->estudiante?->Apellido1 ?? ''))
                    .' en '
                    .($inscripcion->cursoMateria?->materia?->Nombre ?? 'materia'),
                'meta' => optional($inscripcion->Fecha)->diffForHumans(),
                'activity_at' => optional($inscripcion->Fecha)->timestamp ?? 0,
            ]);

        return $recentUsers->concat($recentEnrollments)->sortByDesc('activity_at')->values()->take(6);
    }

    private function teacherActivities(User $user): Collection
    {
        $recentEnrollments = Inscripcion::query()
            ->with(['estudiante', 'cursoMateria.curso', 'cursoMateria.materia'])
            ->whereHas('cursoMateria', function ($query) use ($user) {
                $query->where('IdDocente', $user->IdUsuario);
            })
            ->orderByDesc('Fecha')
            ->limit(6)
            ->get()
            ->map(fn (Inscripcion $inscripcion) => [
                'type' => 'inscripcion',
                'title' => 'Nueva inscripcion',
                'description' => trim(($inscripcion->estudiante?->Nombre1 ?? '').' '.($inscripcion->estudiante?->Apellido1 ?? ''))
                    .' en '
                    .($inscripcion->cursoMateria?->materia?->Nombre ?? 'materia'),
                'meta' => optional($inscripcion->Fecha)->diffForHumans(),
                'activity_at' => optional($inscripcion->Fecha)->timestamp ?? 0,
            ]);

        $recentGrades = Nota::query()
            ->with(['estudiante', 'cursoMateria.materia'])
            ->whereHas('cursoMateria', function ($query) use ($user) {
                $query->where('IdDocente', $user->IdUsuario);
            })
            ->orderByDesc('FechaRegistro')
            ->limit(4)
            ->get()
            ->map(fn (Nota $nota) => [
                'type' => 'nota',
                'title' => 'Nota registrada',
                'description' => trim(($nota->estudiante?->Nombre1 ?? '').' '.($nota->estudiante?->Apellido1 ?? ''))
                    .' obtuvo '
                    .$nota->Nota
                    .' en '
                    .($nota->cursoMateria?->materia?->Nombre ?? 'materia'),
                'meta' => optional($nota->FechaRegistro)->diffForHumans(),
                'activity_at' => optional($nota->FechaRegistro)->timestamp ?? 0,
            ]);

        return $recentEnrollments->concat($recentGrades)->sortByDesc('activity_at')->values()->take(6);
    }

    private function studentActivities(User $user): Collection
    {
        $notifications = Notificacion::query()
            ->where('IdUsuario', $user->IdUsuario)
            ->where('Estado', true)
            ->orderByDesc('FechaEnvio')
            ->limit(4)
            ->get()
            ->map(fn (Notificacion $notification) => [
                'type' => 'notificacion',
                'title' => $notification->Titulo,
                'description' => $notification->Contenido,
                'meta' => optional($notification->FechaEnvio)->diffForHumans(),
                'activity_at' => optional($notification->FechaEnvio)->timestamp ?? 0,
            ]);

        $grades = Nota::query()
            ->with(['cursoMateria.materia'])
            ->where('IdEstudiante', $user->IdUsuario)
            ->where('Estado', true)
            ->orderByDesc('FechaRegistro')
            ->limit(4)
            ->get()
            ->map(fn (Nota $nota) => [
                'type' => 'nota',
                'title' => 'Nota registrada',
                'description' => 'Calificacion de '
                    .($nota->cursoMateria?->materia?->Nombre ?? 'materia')
                    .' por '
                    .$nota->Nota,
                'meta' => optional($nota->FechaRegistro)->diffForHumans(),
                'activity_at' => optional($nota->FechaRegistro)->timestamp ?? 0,
            ]);

        return $notifications->concat($grades)->sortByDesc('activity_at')->values()->take(6);
    }
}
