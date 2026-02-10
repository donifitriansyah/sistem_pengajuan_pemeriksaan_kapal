<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    /**
     * Halaman daftar user (menunggu approval)
     */
    public function index()
    {
        // User yang belum aktif
        $users = User::orderBy('created_at', 'desc')->get();

        return view('pages.admin.user', compact('users'));
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'password' => Hash::make('12345678'),
        ]);

        return back()->with('success', 'Password berhasil direset menjadi 12345678');
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

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required',
            'status' => 'required|in:aktif,nonaktif,ditolak',
        ]);

        $user->update([
            'email' => $request->email,
            'nama_perusahaan' => $request->nama_perusahaan,
            'nama_petugas' => $request->nama_petugas,
            'no_hp' => $request->no_hp,
            'wilayah_kerja' => $request->wilayah_kerja,
            'role' => $request->role,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Data user berhasil diperbarui');
    }
}
