<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu sebagai ' . ucfirst($role) . ' untuk mengakses halaman ini.');
        }

        if (Auth::user()->role !== $role) {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk role ' . strtoupper($role) . '.');
        }

        return $next($request);
    }
}
