<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi input
        $request->validate([
            'nama_petugas' => ['required', 'string', 'max:255'],
            'nama_perusahaan' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'regex:/^[0-9]{9,12}$/'],
            'wilayah_kerja' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:'.User::class,
            ],
            'username' => [
                'required',
                'string',
                'max:255',
                'min:3',
                'unique:'.User::class,
            ],

            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Format nomor HP -> +62xxxxxxxxx
        $noHp = '+62'.ltrim($request->no_hp, '0');

        // Simpan user
        $user = User::create([
            'nama_petugas' => $request->nama_petugas,
            'nama_perusahaan' => $request->nama_perusahaan,
            'no_hp' => $noHp,
            'wilayah_kerja' => $request->wilayah_kerja,
            'role' => 'user', // default
            // Default
            'status' => 'nonaktif',

            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        // Event register (email verification dll)
        event(new Registered($user));

        // ❌ Tidak auto login (karena nonaktif)
        // Auth::login($user);

        // Redirect
        return redirect()
            ->route('login')
            ->with('success', 'Pendaftaran berhasil. Akun menunggu aktivasi admin.');
    }
}
