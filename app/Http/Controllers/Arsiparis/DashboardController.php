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

    public function indexStatus()
    {
        $user = auth()->user();

        // Get the logged-in user's wilayah_kerja
        $wilayah_kerja = $user->wilayah_kerja; // Get the user's wilayah_kerja

        // Fetch all PengajuanPemeriksaanKapal where status is 'menunggu verifikasi',
        // filter by the user's wilayah kerja, and include the related agenda_surat_pengajuan
        $pengajuan = PengajuanPemeriksaanKapal::with(['user', 'agendaSuratPengajuan']) // Eager load 'agendaSuratPengajuan' relation
            ->where('wilayah_kerja', $wilayah_kerja) // Filter by user's wilayah kerja
            ->where('status', 'menunggu verifikasi') // Only include pengajuan with status 'menunggu verifikasi'
            ->latest()
            ->get();

        // Fetch all petugas-kapal (users with role 'petugas-kapal') and filter by wilayah_kerja
        $petugas = User::where('role', 'petugas-kapal')
            ->where('wilayah_kerja', $wilayah_kerja) // Filter petugas by wilayah_kerja
            ->get();

        // Fetch penagihan records, filtering based on wilayah_kerja if necessary
        $penagihanData = Penagihan::with('petugas') // Add relations as needed
            ->whereHas('petugas', function ($query) use ($wilayah_kerja) {
                $query->where('wilayah_kerja', $wilayah_kerja); // Ensure petugas are from the same wilayah_kerja
            })
            ->get();

        // Pass the filtered data to the view
        return view('pages.arsiparis.verifikasi', [
            'pengajuan' => $pengajuan,
            'petugas' => $petugas,
            'penagihanData' => $penagihanData, // Make sure this is passed
        ]);
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
