<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check admin guard first, then fallback to web guard or current request user
        $user = Auth::guard('admin')->user() ?? Auth::guard('web')->user() ?? $request->user();

        if (!$user) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return redirect()->route('admin.login');
            }
            return redirect()->route('login');
        }

        // Cek apakah role user ada di dalam daftar role yang diperbolehkan
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Kalau role tidak sesuai, lemparkan error 403 (Unauthorized)
        abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengakses halaman ini.');
    }
}