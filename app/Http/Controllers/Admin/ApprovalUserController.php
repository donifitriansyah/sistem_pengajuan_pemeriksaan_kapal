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
