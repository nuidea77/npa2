<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle(Request $request, Closure $next, string $role = 'admin'): Response
    {
        $user = $request->user();

        if (!$user || $user->status !== 'active') {
            return response()->json(['message' => 'Нэвтрэх шаардлагатай.'], 401);
        }

        if ($role === 'superadmin' && $user->role !== 'superadmin') {
            return response()->json(['message' => 'Зөвхөн супер админ хандах эрхтэй.'], 403);
        }

        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Хандах эрхгүй.'], 403);
        }

        return $next($request);
    }
}
