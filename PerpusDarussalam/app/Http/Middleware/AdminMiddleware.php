<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek jika login menggunakan Guard 'admin' (tabel admins)
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        // 2. Fallback: Cek jika login menggunakan Guard default 'web' dengan role 'admin' (tabel users)
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // Jika tidak memenuhi kedua kondisi di atas, redirect ke login admin
        return redirect()->route('admin.login')->with('error', 'Anda tidak memiliki akses admin.');
    }
}