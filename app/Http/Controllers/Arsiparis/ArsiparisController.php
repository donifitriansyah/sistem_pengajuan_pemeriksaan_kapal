<?php

namespace App\Http\Controllers\Arsiparis;

use App\Http\Controllers\Controller;
use App\Models\AgendaSuratPengajuan;
use App\Models\PengajuanPemeriksaanKapal;
use Illuminate\Http\Request;

class ArsiparisController extends Controller
{
    public function arsipkan(Request $request, $id)
    {
        // Find the PengajuanPemeriksaanKapal record to archive
        $pengajuan = PengajuanPemeriksaanKapal::findOrFail($id);

        // Get the current year
        $currentYear = date('Y');

        // Generate the nomor surat masuk otomatis
        $lastSuratMasuk = AgendaSuratPengajuan::where('nomor_surat_masuk', 'like', 'AC.01.1/X/%/'.$currentYear)
            ->orderBy('id', 'desc')
            ->first();

        // Extract the last number from nomor_surat_masuk, or set it to 1 if none exists
        $suratMasukNumber = $lastSuratMasuk ? (int) explode('/', $lastSuratMasuk->nomor_surat_masuk)[2] + 1 : 1;
        $nomorSuratMasuk = 'AR.02.01/C.X.1.11/'.str_pad($suratMasukNumber, 4, '0', STR_PAD_LEFT).'/'.$currentYear;

        // Generate the nomor surat keluar otomatis
        $lastSuratKeluar = AgendaSuratPengajuan::where('nomor_surat_keluar', 'like', 'HK.01.1/X/%/'.$currentYear)
            ->orderBy('id', 'desc')
            ->first();

        // Extract the last number from nomor_surat_keluar, or set it to 1 if none exists
        $suratKeluarNumber = $lastSuratKeluar ? (int) explode('/', $lastSuratKeluar->nomor_surat_keluar)[2] + 1 : 1;
        $nomorSuratKeluar = 'SR.04.02/C.X.1.11/'.str_pad($suratKeluarNumber, 4, '0', STR_PAD_LEFT).'/'.$currentYear;

        // Check if the generated nomor_surat_keluar already exists in the database
        $existingSuratKeluar = AgendaSuratPengajuan::where('nomor_surat_keluar', $nomorSuratKeluar)->first();

        // If the nomor_surat_keluar already exists, regenerate it by incrementing the number
        if ($existingSuratKeluar) {
            $suratKeluarNumber++;
            $nomorSuratKeluar = 'SR.04.02/C.X.1.11/'.str_pad($suratKeluarNumber, 4, '0', STR_PAD_LEFT).'/'.$currentYear;
        }

        // Create a new AgendaSuratPengajuan record
        $agenda = AgendaSuratPengajuan::create([
            'nomor_surat_pengajuan' => $request->input('nomor_surat_pengajuan'),
            'nomor_surat_masuk' => $nomorSuratMasuk,  // Automatically generated
            'nomor_surat_keluar' => $nomorSuratKeluar, // Automatically generated and ensured unique
            'tanggal_surat' => $request->input('tanggal_surat'),
        ]);

        // Update the PengajuanPemeriksaanKapal record with the new agenda_surat_pengajuan_id
        $pengajuan->update([
            'agenda_surat_pengajuan_id' => $agenda->id, // Link the new AgendaSuratPengajuan
        ]);

        // Redirect back with a success message
        return back()->with('success', 'Pengajuan berhasil diarsipkan.');
    }
}
