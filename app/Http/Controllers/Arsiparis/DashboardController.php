<?php

namespace App\Http\Controllers\Arsiparis;

use App\Http\Controllers\Controller;
use App\Models\Penagihan;
use App\Models\PengajuanPemeriksaanKapal;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Get the logged-in user's wilayah_kerja
        $user = auth()->user();
        $wilayah_kerja = $user->wilayah_kerja; // Get the user's wilayah_kerja
        $pengajuan = PengajuanPemeriksaanKapal::with('user')
            ->whereNull('agenda_surat_pengajuan_id') // Not yet diagendakan
            ->where('wilayah_kerja', $wilayah_kerja) // Filter by user's wilayah_kerja
            ->where('status', 'diterima') // Filter by 'status' field to show only 'diterima'
            ->latest()
            ->get();
        // Total Pengajuan - Filter all pengajuan where status 'diterima' and wilayah_kerja matches user
        $totalPengajuan = PengajuanPemeriksaanKapal::where('wilayah_kerja', $wilayah_kerja)
            ->where('status', 'diterima')
            ->count();

        // Belum Diagendakan - Pengajuan where agenda_surat_pengajuan_id is null
        $totalBelumTagihan = PengajuanPemeriksaanKapal::where('wilayah_kerja', $wilayah_kerja)
            ->whereNull('agenda_surat_pengajuan_id')
            ->where('status', 'diterima')
            ->count();

        // Butuh Verifikasi - Pengajuan where status pembayaran is 'menunggu'
        $totalBelumBayar = PengajuanPemeriksaanKapal::where('wilayah_kerja', $wilayah_kerja)
            ->where('status', 'Menunggu Verifikasi')

            ->count();

        // Surat Masuk - Pengajuan with 'nomor_surat_masuk' from agenda_surat_pengajuan
        $totalMenunggu = PengajuanPemeriksaanKapal::where('wilayah_kerja', $wilayah_kerja)
            ->where('status', 'diterima')
            ->whereHas('agendaSuratPengajuan', function ($query) {
                $query->whereNotNull('nomor_surat_masuk'); // Check if nomor_surat_masuk is present
            })
            ->count();

        // Surat Keluar - Pengajuan with 'nomor_surat_keluar' from agenda_surat_pengajuan
        $totalLunas = PengajuanPemeriksaanKapal::where('wilayah_kerja', $wilayah_kerja)
            ->where('status', 'diterima')
            ->whereHas('agendaSuratPengajuan', function ($query) {
                $query->whereNotNull('nomor_surat_keluar'); // Check if nomor_surat_keluar is present
            })
            ->count();

        // Pass the filtered data to the view
        return view('pages.arsiparis.dashboard', compact(
            'totalPengajuan',
            'totalBelumTagihan',
            'totalBelumBayar',
            'totalMenunggu',
            'totalLunas',
            'pengajuan'
        ));
    }

    
    public function indexStatus()
    {
        $user = auth()->user();

        // Get the logged-in user's wilayah_kerja
        $wilayah_kerja = $user->wilayah_kerja;

        // Fetch all PengajuanPemeriksaanKapal where status is 'menunggu verifikasi',
        // filter by the user's wilayah kerja, and include the related agenda_surat_pengajuan
        $pengajuan = PengajuanPemeriksaanKapal::with(['user', 'agendaSuratPengajuan'])
            ->where('wilayah_kerja', $wilayah_kerja)
            ->where('status', 'menunggu verifikasi')
            ->latest()
            ->get();

        // Fetch all petugas-kapal (users with role 'petugas-kapal') and filter by wilayah_kerja
        $petugas = User::where('role', 'petugas-kapal')
            ->where('wilayah_kerja', $wilayah_kerja)
            ->get();

        // Fetch penagihan records, filtering based on wilayah_kerja if necessary
        $penagihanData = Penagihan::with('petugas')
            ->whereHas('petugas', function ($query) use ($wilayah_kerja) {
                $query->where('wilayah_kerja', $wilayah_kerja);
            })
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

        // Pass the filtered data to the view using compact
        return view('pages.arsiparis.verifikasi', compact(
            'totalPengajuan',
            'totalBelumTagihan',
            'totalBelumBayar',
            'totalMenunggu',
            'totalLunas',
            'pengajuan',
            'petugas',
            'penagihanData'
        ));
    }

    public function indexSudahDiagendakan()
    {
        // Get the logged-in user's wilayah_kerja
        $user = auth()->user();
        $wilayah_kerja = $user->wilayah_kerja; // Get the user's wilayah_kerja

        // Fetch PengajuanPemeriksaanKapal that have an 'agenda_surat_pengajuan_id' (i.e., sudah diagendakan)
        // and filter by the user's wilayah_kerja
        $pengajuan = PengajuanPemeriksaanKapal::with('user')
            ->whereNotNull('agenda_surat_pengajuan_id') // Already scheduled
            ->where('wilayah_kerja', $wilayah_kerja) // Filter by user's wilayah_kerja
            ->latest()
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

        // Pass the filtered data to the view
        return view('pages.arsiparis.sudah-diagendakan', compact(
            'pengajuan',
            'totalPengajuan',
            'totalBelumTagihan',
            'totalBelumBayar',
            'totalMenunggu',
            'totalLunas',
            ));
    }
}
