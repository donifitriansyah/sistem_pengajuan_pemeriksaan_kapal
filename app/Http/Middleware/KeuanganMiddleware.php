<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class KeuanganMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Cek role
        if (Auth::user()->role !== 'bendahara_wilker') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
