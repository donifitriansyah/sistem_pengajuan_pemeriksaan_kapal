<?php

namespace App\Http\Controllers\Arsiparis;

use App\Http\Controllers\Controller;
use App\Models\AgendaSuratPengajuan;
use App\Models\PengajuanPemeriksaanKapal;
use Illuminate\Http\Request;

class SuratKeluarController extends Controller
{
    public function index()
    {
         $user = auth()->user();
        $wilayah_kerja = $user->wilayah_kerja; // Get the user's wilayah_kerja
        $suratKeluar = AgendaSuratPengajuan::with(['pengajuan.user'])
            ->whereNotNull('nomor_surat_keluar')
            ->orderBy('tanggal_surat', 'desc')
            ->get();

             // Total Pengajuan
        $totalPengajuan = PengajuanPemeriksaanKapal::where('wilayah_kerja', $wilayah_kerja)
            ->where('status', 'diterima')
            ->count();

        // Belum Diagendakan
        $totalBelumTagihan = PengajuanPemeriksaanKapal::where('wilayah_kerja', $wilayah_kerja)
            ->whereNull('agenda_surat_pengajuan_id')
            ->where('status', 'diterima')
            ->count();

        // Butuh Verifikasi
        $totalBelumBayar = PengajuanPemeriksaanKapal::where('wilayah_kerja', $wilayah_kerja)
            ->where('status', 'Menunggu Verifikasi')

            ->count();

        // Surat Masuk
        $totalMenunggu = PengajuanPemeriksaanKapal::where('wilayah_kerja', $wilayah_kerja)
            ->where('status', 'diterima')
            ->whereHas('agendaSuratPengajuan', function ($query) {
                $query->whereNotNull('nomor_surat_masuk');
            })
            ->count();

        // Surat Keluar
        $totalLunas = PengajuanPemeriksaanKapal::where('wilayah_kerja', $wilayah_kerja)
            ->where('status', 'diterima')
            ->whereHas('agendaSuratPengajuan', function ($query) {
                $query->whereNotNull('nomor_surat_keluar');
            })
            ->count();

        return view('pages.arsiparis.surat-keluar', compact(
            'suratKeluar',
            'totalPengajuan',
            'totalBelumTagihan',
            'totalBelumBayar',
            'totalMenunggu',
            'totalLunas',
            ));
    }
}
