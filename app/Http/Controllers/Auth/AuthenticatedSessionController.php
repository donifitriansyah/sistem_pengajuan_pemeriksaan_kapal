<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();

        // ❌ JIKA STATUS BUKAN AKTIF
        if ($user->status !== 'aktif') {

            auth()->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Akun Anda belum aktif atau ditolak. Silakan hubungi admin.',
            ]);
        }

        // ✅ JIKA STATUS AKTIF → CEK ROLE
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'arsiparis_wilker') {
            return redirect()->route('arsiparis.verifikasi');
        }

        if ($user->role === 'kawilker') {
            return redirect()->route('petugas.dashboard');
        }

        if ($user->role === 'bendahara_wilker') {
            return redirect()->route('petugas.pembayaran');
        }

        // Default user
        return redirect()->route('user.dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
