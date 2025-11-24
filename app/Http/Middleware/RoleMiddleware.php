<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Contoh penggunaan:
     * - 'role:admin'
     * - 'role:admin,vendor'
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user() ?? Auth::user();

        // kalau belum login, biar middleware 'auth' yang handle redirect
        if (! $user) {
            return redirect()->route('login');
        }

        // cek apakah role user ada di list roles yang diizinkan
        if (! in_array($user->role, $roles, true)) {
            // boleh 403/404, tadi lo pakai 404
            abort(404);
        }

        return $next($request);
    }
}
