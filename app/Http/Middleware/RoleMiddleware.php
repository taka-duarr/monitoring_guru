<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $userRole = Auth::user()->jabatan;

        if ($userRole !== $role) {
            // Jika role tidak cocok, redirect ke dashboard yang sesuai
            if ($userRole === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($userRole === 'guru') {
                return redirect()->route('guru.dashboard');
            } elseif ($userRole === 'ketuakelas') {
                return redirect()->route('siswa.dashboard');
            }
            
            return redirect('/');
        }

        return $next($request);
    }
}
