<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Penagihan;
use App\Models\PengajuanPemeriksaanKapal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenugasanController extends Controller
{
    // public function store(Request $request, $pengajuanId)
    // {
    //     // dd($request->all()); // ← ini sudah OK, boleh hapus nanti

    //     $request->validate([
    //         'jumlah_petugas' => 'required|integer|min:1',
    //         'petugas' => 'required|array',
    //         'petugas.*' => 'exists:users,id',
    //         'jenis_tarif' => 'required',
    //         'total_tarif' => 'required|numeric',
    //         'waktu_mulai' => 'required|date',
    //         'waktu_selesai' => 'required|date|after:waktu_mulai',
    //     ]);

    //     DB::transaction(function () use ($request, $pengajuanId) {

    //         // 1️⃣ SIMPAN PENAGIHAN (INI WAJIB PERTAMA)
    //         $penagihan = Penagihan::create([
    //             'pengajuan_id' => $pengajuanId,
    //             'jenis_tarif' => $request->jenis_tarif,
    //             'jumlah_petugas' => $request->jumlah_petugas,
    //             'waktu_mulai' => $request->waktu_mulai,
    //             'waktu_selesai' => $request->waktu_selesai,
    //             'total_tarif' => $request->total_tarif,
    //         ]);

    //         // 2️⃣ SIMPAN PETUGAS KE PIVOT (INI YANG SERING TERLEWAT)
    //         $penagihan->petugas()->sync($request->petugas);

    //         // 3️⃣ (OPSIONAL) update pengajuan
    //         PengajuanPemeriksaanKapal::where('id', $pengajuanId)
    //             ->update(['penagihan_id' => $penagihan->id]);
    //     });

    //     return redirect()->back()->with('success', 'Penagihan berhasil dibuat');
    // }
    public function store(Request $request, $pengajuanId)
    {
        // Validate the required fields
        $request->validate([
            'jumlah_petugas' => 'required|integer|min:1',
            'petugas' => 'required|array',
            'petugas.*' => 'exists:users,id',
            'jenis_tarif' => 'required',
            'total_tarif' => 'required|numeric',  // total_tarif is a number, not a string
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
        ]);

        // Start a transaction for atomicity
        DB::transaction(function () use ($request, $pengajuanId) {

            // Retrieve relevant fields from the request
            $jumlahPetugas = $request->input('jumlah_petugas');
            $petugas = $request->input('petugas');  // This is an array of petugas IDs
            $jenisTarif = $request->input('jenis_tarif');
            $totalTarif = $request->input('total_tarif');
            $waktuMulai = \Carbon\Carbon::parse($request->input('waktu_mulai'));
            $waktuSelesai = \Carbon\Carbon::parse($request->input('waktu_selesai'));

            // Calculate the number of days between the start and end times
            $daysDifference = $waktuMulai->diffInDays($waktuSelesai) + 1; // Add 1 to include both start and end days

            // Store Penagihan data with the formatted total_tarif
            $penagihan = Penagihan::create([
                'pengajuan_id' => $pengajuanId,
                'jenis_tarif' => $jenisTarif,
                'jumlah_petugas' => $jumlahPetugas,
                'waktu_mulai' => $waktuMulai,
                'waktu_selesai' => $waktuSelesai,
                'total_tarif' => $totalTarif, // Store the total_tarif as a raw numeric value
            ]);

            // Save Petugas to pivot table (assuming this is a many-to-many relationship)
            $penagihan->petugas()->sync($petugas);

            // Update the pengajuan record with the new penagihan_id
            PengajuanPemeriksaanKapal::where('id', $pengajuanId)
                ->update(['penagihan_id' => $penagihan->id]);
        });

        // Return success response
        return redirect()->back()->with('success', 'Penagihan berhasil dibuat');
    }
    public function storeKeuangan(Request $request, $pengajuanId)
    {
        // Validate the required fields
        $request->validate([
            'jumlah_petugas' => 'required|integer|min:1',
            'petugas' => 'required|array',
            'petugas.*' => 'exists:users,id',
            'jenis_tarif' => 'required',
            'total_tarif' => 'required|numeric',  // total_tarif is a number, not a string
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
        ]);

        // Start a transaction for atomicity
        DB::transaction(function () use ($request, $pengajuanId) {

            // Retrieve relevant fields from the request
            $jumlahPetugas = $request->input('jumlah_petugas');
            $petugas = $request->input('petugas');  // This is an array of petugas IDs
            $jenisTarif = $request->input('jenis_tarif');
            $totalTarif = $request->input('total_tarif');
            $waktuMulai = \Carbon\Carbon::parse($request->input('waktu_mulai'));
            $waktuSelesai = \Carbon\Carbon::parse($request->input('waktu_selesai'));

            // Calculate the number of days between the start and end times
            $daysDifference = $waktuMulai->diffInDays($waktuSelesai) + 1; // Add 1 to include both start and end days

            // Store Penagihan data with the formatted total_tarif
            $penagihan = Penagihan::create([
                'pengajuan_id' => $pengajuanId,
                'jenis_tarif' => $jenisTarif,
                'jumlah_petugas' => $jumlahPetugas,
                'waktu_mulai' => $waktuMulai,
                'waktu_selesai' => $waktuSelesai,
                'total_tarif' => $totalTarif, // Store the total_tarif as a raw numeric value
            ]);

            // Save Petugas to pivot table (assuming this is a many-to-many relationship)
            $penagihan->petugas()->sync($petugas);

            // Update the pengajuan record with the new penagihan_id
            PengajuanPemeriksaanKapal::where('id', $pengajuanId)
                ->update(['penagihan_id' => $penagihan->id]);
        });

        // Return success response
        return redirect()->back()->with('success', 'Penagihan berhasil dibuat');
    }
}
