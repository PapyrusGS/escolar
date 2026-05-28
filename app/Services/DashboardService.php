<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\DashboardRepository;
use RuntimeException;
use Throwable;

class DashboardService
{
    public function __construct(
        private readonly DashboardRepository $dashboardRepository
    ) {
    }

    public function show(User $user): array
    {
        try {
            return [
                'status' => true,
                'message' => 'Dashboard cargado correctamente.',
                'data' => [
                    'summary' => $this->dashboardRepository->summary($user),
                    'recent_activities' => $this->dashboardRepository->recentActivities($user)->values(),
                    'relevant_info' => $this->dashboardRepository->relevantInfo($user),
                ],
            ];
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('No se pudo cargar el dashboard.');
        }
    }
}
