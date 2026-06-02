<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeacherRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'No autenticado.',
                'data' => [],
            ], 401);
        }

        if ((int) $user->IdRol !== 2) {
            return response()->json([
                'status' => false,
                'message' => 'No tienes permisos para acceder a este recurso.',
                'data' => [],
            ], 403);
        }

        return $next($request);
    }
}
