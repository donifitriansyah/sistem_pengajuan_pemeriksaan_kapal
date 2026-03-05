<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\AgendaSuratPengajuan;
use App\Models\Pembayaran;
use App\Models\Penagihan;
use App\Models\PengajuanPemeriksaanKapal;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardPetugasKapalController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (! $user || ! $user->wilayah_kerja) {
            abort(403, 'Wilayah kerja tidak ditemukan.');
        }

        $pengajuan = PengajuanPemeriksaanKapal::with([
            'user',
            'penagihan.pembayaran',
            'penagihan.petugas',
            'agendaSuratPengajuan',
        ])
            ->where('wilayah_kerja', $user->wilayah_kerja)

            // 🔥 hanya yang SUDAH ADA penagihan
            ->has('penagihan')

            ->orderByDesc('created_at')
            ->get();

        $petugas = User::where('wilayah_kerja', $user->wilayah_kerja)
            ->whereNotIn('role', ['admin', 'user'])
            ->get();

        return view('pages.petugas-kapal.pemeriksa', [
            'pengajuan' => $pengajuan,
            'petugas' => $petugas,
        ]);
    }
}
