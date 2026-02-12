<?php

namespace App\Http\Controllers\Arsiparis;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPemeriksaanKapal;

class DashboardController extends Controller
{
    public function index()
    {
        // Get the logged-in user's wilayah_kerja
        $user = auth()->user();
        $wilayah_kerja = $user->wilayah_kerja; // Get the user's wilayah_kerja

        // Fetch PengajuanPemeriksaanKapal that don't have an 'agenda_surat_pengajuan_id' (i.e., belum diagendakan)
        // and filter by the user's wilayah_kerja and where status is 'diterima'
        $pengajuan = PengajuanPemeriksaanKapal::with('user')
            ->whereNull('agenda_surat_pengajuan_id') // Not yet diagendakan
            ->where('wilayah_kerja', $wilayah_kerja) // Filter by user's wilayah_kerja
            ->where('status', 'diterima') // Filter by 'status' field to show only 'diterima'
            ->latest()
            ->get();

        // Pass the filtered data to the view
        return view('pages.arsiparis.dashboard', compact('pengajuan'));
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

        // Pass the filtered data to the view
        return view('pages.arsiparis.sudah-diagendakan', compact('pengajuan'));
    }
}
