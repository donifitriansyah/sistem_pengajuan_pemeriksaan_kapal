<?php

namespace App\Http\Controllers\Arsiparis;

use App\Http\Controllers\Controller;
use App\Models\AgendaSuratPengajuan;

class SuratMasukController extends Controller
{
    public function index()
    {
        $suratKeluar = AgendaSuratPengajuan::with(['pengajuan.user'])
            ->whereNotNull('nomor_surat_keluar')
            ->orderBy('tanggal_surat', 'desc')
            ->get();

        return view('pages.arsiparis.surat-masuk', compact('suratKeluar'));
    }
}
