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
        $user = Auth::user();

        // Tolak jika belum login atau role tidak diizinkan
        if (!$user || !in_array($user->role, $roles)) {
            return redirect()->route('home')
                ->with('error', 'Akses ditolak! Anda tidak memiliki izin untuk mengakses halaman tersebut.');
        }

        return $next($request);
    }
}