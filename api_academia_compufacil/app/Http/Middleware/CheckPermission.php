<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckPermission
{
    public function handle($request, Closure $next, $permission)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $hasPermission = DB::table('role_permission')
            ->join('user_roles', 'role_permission.role_id', '=', 'user_roles.role_id')
            ->join('permissions', 'role_permission.permission_id', '=', 'permissions.id')
            ->where('user_roles.user_id', $user->id)
            ->where('permissions.name', $permission)
            ->exists();

        if (!$hasPermission) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        return $next($request);
    }
}