<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class ApprovalUserController extends Controller
{
    public function index()
    {
        // User yang belum aktif
        $users = User::orderBy('created_at', 'desc')->get();

        return view('pages.admin.approval', compact('users'));
    }

    public function indexPetugas()
{
    // Mendapatkan Wilker pengguna yang login
    $wilkerLogin = auth()->user()->wilayah_kerja; // Asumsi kolom 'wilker' ada di tabel users

    // Menyaring pengguna dengan status 'nonaktif' dan wilker yang sama dengan petugas yang login
    $users = User::where('status', 'nonaktif')  // Menyaring berdasarkan status 'nonaktif'
                 ->where('wilayah_kerja', $wilkerLogin) // Menyaring berdasarkan wilker petugas yang login
                 ->orderBy('created_at', 'desc') // Mengurutkan berdasarkan waktu pembuatan
                 ->get();

    return view('pages.petugas.approval', compact('users'));
}


    public function approve($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'status' => 'aktif',
        ]);

        return back()->with('success', 'User berhasil disetujui');
    }

    /**
     * Tolak user (hapus / nonaktifkan)
     */
    public function reject($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'status' => 'ditolak',
        ]);

        return back()->with('success', 'User berhasil ditolak');
    }
}
