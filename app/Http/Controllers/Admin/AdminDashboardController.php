<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPemeriksaanKapal;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
{
    $pengajuanPemeriksaans = PengajuanPemeriksaanKapal::latest()->get();

    return view('pages.admin.daftar-pengajuan', compact('pengajuanPemeriksaans'));
}

    public function destroy($id)
    {
        $pengajuan = PengajuanPemeriksaanKapal::findOrFail($id);

        $pengajuan->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Data pengajuan berhasil dihapus.');
    }
}
