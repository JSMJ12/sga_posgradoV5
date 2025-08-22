<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAnyPermission
{
    public function handle($request, Closure $next, ...$permissions)
    {
        $user = Auth::user();

        foreach ($permissions as $permission) {
            if ($user->can($permission)) {
                return $next($request);
            }
        }

        abort(403);
    }
}
