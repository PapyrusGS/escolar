<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeacherRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if (! $usuario || (int) $usuario->IdRol !== 2) {
            return response()->json([
                'status' => false,
                'message' => 'No tienes permisos para realizar esta acción.',
                'data' => [],
            ], 403);
        }

        return $next($request);
    }
}
