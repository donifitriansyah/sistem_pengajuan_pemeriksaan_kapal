<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPemeriksaanKapal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
    // public function store(Request $request)
    // {
    //     // 1. Validasi
    //     $request->validate([
    //         'tgl_estimasi_pemeriksaan' => 'required|date',
    //         'nama_kapal' => 'required|string|max:255',
    //         'lokasi_kapal' => 'required|string|max:255',
    //         'jenis_dokumen' => 'required|in:PHQC,SSCEC,COP',
    //         'wilayah_kerja' => 'required|in:Dwikora,Kijing,Padang Tikar,Teluk Batang,Ketapang,Kendawangan',
    //         'surat_permohonan' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
    //         'waktu_kedatangan_kapal' => 'required|date_format:H:i',  // Validate the time format
    //     ]);

    //     // 2. Mapping wilayah → kode
    //     $kodeWilker = [
    //         'Dwikora' => 'DW',
    //         'Kijing' => 'KJ',
    //         'Padang Tikar' => 'PT',
    //         'Teluk Batang' => 'TB',
    //         'Ketapang' => 'KT',
    //         'Kendawangan' => 'KD',
    //     ];

    //     $wilayah = $request->wilayah_kerja;
    //     $prefix = $kodeWilker[$wilayah] ?? 'XX';

    //     // 3. Generate kode bayar (anti duplikat)
    //     do {
    //         $angkaRandom = rand(1000000, 9999999);
    //         $kodeBayar = $prefix.'-'.$angkaRandom;
    //     } while (
    //         PengajuanPemeriksaanKapal::where('kode_bayar', $kodeBayar)->exists()
    //     );

    //     // 4. Upload file
    //     $file = $request->file('surat_permohonan');
    //     $fileName = time().'_'.$file->getClientOriginalName();
    //     $path = $file->storeAs('surat_permohonan', $fileName, 'public');

    //     // 5. Parse the waktu_kedatangan_kapal to store in the database
    //     $waktuKedatanganKapal = \Carbon\Carbon::parse($request->waktu_kedatangan_kapal)->format('H:i');  // Store just the time in 'H:i' format

    //     // 6. Simpan ke database
    //     PengajuanPemeriksaanKapal::create([
    //         'tgl_estimasi_pemeriksaan' => $request->tgl_estimasi_pemeriksaan,
    //         'nama_kapal' => $request->nama_kapal,
    //         'lokasi_kapal' => $request->lokasi_kapal,
    //         'jenis_dokumen' => $request->jenis_dokumen,
    //         'wilayah_kerja' => $wilayah,
    //         'surat_permohonan_dan_dokumen' => $path,
    //         'kode_bayar' => $kodeBayar,
    //         'user_id' => Auth::id(),
    //         'penagihan_id' => null,
    //         'agenda_surat_pengajuan_id' => null,
    //         'waktu_kedatangan_kapal' => $waktuKedatanganKapal,  // Store the time only
    //     ]);

    //     // 7. Redirect
    //     return redirect()
    //         ->back()
    //         ->with('success', 'Pengajuan berhasil dikirim. Kode Bayar: '.$kodeBayar);
    // }
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'tgl_estimasi_pemeriksaan' => 'required|date',
            'nama_kapal' => 'required|string|max:255',
            'lokasi_kapal' => 'required|string|max:255',
            'jenis_dokumen' => 'required|in:PHQC,SSCEC,COP,P3K',
            'wilayah_kerja' => 'required|in:Dwikora,Kijing,Padang Tikar,Teluk Batang,Ketapang,Kendawangan',
            'surat_permohonan' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'waktu_kedatangan_kapal' => 'required|date',  // Validate the time format
            'status' => 'nullable|string|in:Menunggu Verifikasi,Diterima,Ditolak',  // Validate the 'status' field
            'keterangan' => 'nullable|string',  // Validate the 'keterangan' field (optional)
        ]);

        // 2. Mapping wilayah → kode
        $kodeWilker = [
            'Dwikora' => 'DW',
            'Kijing' => 'KJ',
            'Padang Tikar' => 'PT',
            'Teluk Batang' => 'TB',
            'Ketapang' => 'KT',
            'Kendawangan' => 'KD',
        ];

        $wilayah = $request->wilayah_kerja;
        $prefix = $kodeWilker[$wilayah] ?? 'XX';

        // 3. Generate kode bayar (anti duplikat)
        do {
            $angkaRandom = rand(1000000, 9999999);
            $kodeBayar = $prefix.'-'.$angkaRandom;
        } while (
            PengajuanPemeriksaanKapal::where('kode_bayar', $kodeBayar)->exists()
        );

        // 4. Upload file
        $file = $request->file('surat_permohonan');
        $fileName = time().'_'.$file->getClientOriginalName();
        $path = $file->storeAs('surat_permohonan', $fileName, 'public');

        // 5. Parse the waktu_kedatangan_kapal to store in the database
        $waktuKedatanganKapal = \Carbon\Carbon::parse($request->waktu_kedatangan_kapal)->format('H:i');  // Store just the time in 'H:i' format

        // 6. Determine the 'status' value, default to 'menunggu verifikasi' if not provided
        $status = $request->status ?? 'menunggu verifikasi';

        // 7. Simpan ke database
        PengajuanPemeriksaanKapal::create([
            'tgl_estimasi_pemeriksaan' => $request->tgl_estimasi_pemeriksaan,
            'nama_kapal' => $request->nama_kapal,
            'lokasi_kapal' => $request->lokasi_kapal,
            'jenis_dokumen' => $request->jenis_dokumen,
            'wilayah_kerja' => $wilayah,
            'surat_permohonan_dan_dokumen' => $path,
            'kode_bayar' => $kodeBayar,
            'user_id' => Auth::id(),
            'penagihan_id' => null,
            'agenda_surat_pengajuan_id' => null,
            'waktu_kedatangan_kapal' => $waktuKedatanganKapal,  // Store the time only
            'status' => $status,  // Store the 'status' field
            'keterangan' => $request->keterangan,  // Store the 'keterangan' field (nullable)
        ]);

        // 8. Redirect
        return redirect()
            ->back()
            ->with('success', 'Pengajuan berhasil dikirim. Kode Bayar: '.$kodeBayar);
    }
}
