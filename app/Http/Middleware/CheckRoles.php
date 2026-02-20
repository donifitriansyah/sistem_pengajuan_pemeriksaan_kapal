<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRoles
{
    public function handle(Request $request, Closure $next, $roles)
    {
        // Ambil peran-peran dari parameter middleware
        $roles = explode('|', $roles);

        // Periksa apakah pengguna memiliki salah satu peran
        if (!in_array(auth()->user()->role, $roles)) {
            // Jika tidak, arahkan ke halaman yang tidak diizinkan
            return redirect('/unauthorized');
        }

        return $next($request);
    }
}
