<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Penagihan;
use App\Models\PengajuanPemeriksaanKapal;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardPetugasController extends Controller
{
    // public function index()
    // {
    //     // Fetch PengajuanPemeriksaanKapal that are not yet billed (penagihan_id is null) and already have an agenda
    //     $pengajuan = PengajuanPemeriksaanKapal::whereNull('penagihan_id') // Not yet billed
    //         ->whereNotNull('agenda_surat_pengajuan_id') // Already scheduled
    //         ->get();

    //     // Fetch all petugas-kapal (or any user with the role 'petugas-kapal')
    //     $petugas = User::where('role', 'petugas-kapal')->get();

    //     // Fetch all penagihan records (to check which petugas are assigned)
    //     $penagihanData = Penagihan::with('petugas') // You can add relations if needed, like 'petugas'
    //         ->get();

    //     // Pass the data to the view
    //     return view('pages.petugas.dashboard', [
    //         'pengajuan' => $pengajuan,
    //         'petugas' => $petugas,
    //         'penagihanData' => $penagihanData, // Make sure this is passed
    //     ]);
    // }
    // public function index()
    // {
    //     // Get the logged-in user
    //     $user = auth()->user();

    //     // Check if the user has a valid wilayah kerja (assuming the 'wilayah_kerja' field is in the 'users' table)
    //     $wilayah_kerja = $user->wilayah_kerja;

    //     // Fetch PengajuanPemeriksaanKapal that are not yet billed (penagihan_id is null)
    //     // and already have an agenda, filtering by the user's wilayah kerja
    //     $pengajuan = PengajuanPemeriksaanKapal::whereNull('penagihan_id') // Not yet billed
    //         ->whereNotNull('agenda_surat_pengajuan_id') // Already scheduled
    //         ->where('wilayah_kerja', $wilayah_kerja) // Filter by user's wilayah kerja
    //         ->get();

    //     // Fetch all petugas-kapal (or any user with the role 'petugas-kapal')
    //     $petugas = User::where('role', 'petugas-kapal')->get();

    //     // Fetch all penagihan records (to check which petugas are assigned)
    //     $penagihanData = Penagihan::with('petugas') // You can add relations if needed, like 'petugas'
    //         ->get();

    //     // Pass the data to the view
    //     return view('pages.petugas.dashboard', [
    //         'pengajuan' => $pengajuan,
    //         'petugas' => $petugas,
    //         'penagihanData' => $penagihanData, // Make sure this is passed
    //     ]);
    // }
    // public function index()
    // {
    //     // Get the logged-in user
    //     $user = auth()->user();

    //     // Get the logged-in user's wilayah_kerja
    //     $wilayah_kerja = $user->wilayah_kerja;

    //     // Fetch PengajuanPemeriksaanKapal that are not yet billed (penagihan_id is null)
    //     // and already have an agenda, filtering by the user's wilayah kerja
    //     $pengajuan = PengajuanPemeriksaanKapal::whereNull('penagihan_id') // Not yet billed
    //         ->whereNotNull('agenda_surat_pengajuan_id') // Already scheduled
    //         ->where('wilayah_kerja', $wilayah_kerja) // Filter by user's wilayah kerja
    //         ->get();

    //     // Fetch petugas-kapal (users with role 'petugas-kapal') and filter by wilayah_kerja
    //     $petugas = User::where('role', 'petugas-kapal')
    //         ->where('wilayah_kerja', $wilayah_kerja) // Filter petugas by wilayah_kerja
    //         ->get();

    //     // Fetch penagihan records, filtering based on wilayah_kerja if necessary
    //     $penagihanData = Penagihan::with('petugas') // Add relations as needed
    //         ->whereHas('petugas', function ($query) use ($wilayah_kerja) {
    //             $query->where('wilayah_kerja', $wilayah_kerja); // Ensure petugas are from the same wilayah_kerja
    //         })
    //         ->get();

    //     // Pass the data to the view
    //     return view('pages.petugas.dashboard', [
    //         'pengajuan' => $pengajuan,
    //         'petugas' => $petugas,
    //         'penagihanData' => $penagihanData, // Make sure this is passed
    //     ]);
    // }

    // public function index()
    // {
    //     // Get the logged-in user
    //     $user = auth()->user();

    //     // Get the logged-in user's wilayah_kerja
    //     $wilayah_kerja = $user->wilayah_kerja; // Get the user's wilayah_kerja

    //     // Fetch all PengajuanPemeriksaanKapal, regardless of whether they have an agenda or not
    //     // and filter by the user's wilayah kerja, including the related agenda_surat_pengajuan
    //     $pengajuan = PengajuanPemeriksaanKapal::with(['user', 'agendaSuratPengajuan']) // Eager load 'agendaSuratPengajuan' relation
    //         ->where('wilayah_kerja', $wilayah_kerja) // Filter by user's wilayah kerja
    //         ->latest()
    //         ->get();

    //     // Fetch all petugas-kapal (users with role 'petugas-kapal') and filter by wilayah_kerja
    //     $petugas = User::where('role', 'petugas-kapal')
    //         ->where('wilayah_kerja', $wilayah_kerja) // Filter petugas by wilayah_kerja
    //         ->get();

    //     // Fetch penagihan records, filtering based on wilayah_kerja if necessary
    //     $penagihanData = Penagihan::with('petugas') // Add relations as needed
    //         ->whereHas('petugas', function ($query) use ($wilayah_kerja) {
    //             $query->where('wilayah_kerja', $wilayah_kerja); // Ensure petugas are from the same wilayah_kerja
    //         })
    //         ->get();

    //     // Pass the filtered data to the view
    //     return view('pages.petugas.dashboard', [
    //         'pengajuan' => $pengajuan,
    //         'petugas' => $petugas,
    //         'penagihanData' => $penagihanData, // Make sure this is passed
    //     ]);
    // }
    public function index()
    {
        // Get the logged-in user
        $user = auth()->user();

        // Get the logged-in user's wilayah_kerja
        $wilayah_kerja = $user->wilayah_kerja; // Get the user's wilayah_kerja

        // Fetch all PengajuanPemeriksaanKapal, regardless of whether they have an agenda or not
        // and filter by the user's wilayah kerja, including the related agenda_surat_pengajuan
        $pengajuan = PengajuanPemeriksaanKapal::with(['user', 'agendaSuratPengajuan']) // Eager load 'agendaSuratPengajuan' relation
            ->where('wilayah_kerja', $wilayah_kerja) // Filter by user's wilayah kerja
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
        return view('pages.petugas.dashboard', [
            'pengajuan' => $pengajuan,
            'petugas' => $petugas,
            'penagihanData' => $penagihanData, // Make sure this is passed
        ]);
    }

    public function indexPengajuan()
    {
        return view('pages.petugas.pengajuan', [
            'pengajuan' => PengajuanPemeriksaanKapal::with(['user', 'penagihan', 'agendaSuratPengajuan'])->get(),
            'petugas' => User::where('role', 'petugas')->get(),
        ]);
    }

    public function indexPembayaran()
    {
        return view('pages.petugas.pembayaran', [
            'pengajuan' => PengajuanPemeriksaanKapal::with([
                'user',
                'penagihan.pembayaran',
                'agendaSuratPengajuan',
            ])
                ->get(),
        ]);
    }

    // public function indexPemeriksa()
    // {
    //     return view('pages.petugas.pemeriksa', [
    //         'pengajuan' => PengajuanPemeriksaanKapal::with([
    //             'user',
    //             'penagihan.pembayaran',
    //             'penagihan.petugas',  // Add this to load petugas data
    //             'agendaSuratPengajuan',
    //         ])
    //             ->get(),
    //     ]);
    // }
    public function indexPemeriksa()
    {
        // Mengambil semua pengajuan beserta data yang diperlukan
        $pengajuan = PengajuanPemeriksaanKapal::with([
            'user',
            'penagihan.pembayaran',
            'penagihan.petugas',  // Memuat petugas
            'agendaSuratPengajuan',
        ])->get();

        // Mengambil pengguna dengan role 'petugas'
        $petugas = User::where('role', 'petugas-kapal')->get();

        return view('pages.petugas.pemeriksa', [
            'pengajuan' => $pengajuan,
            'petugas' => $petugas, // Mengirim data petugas ke view
        ]);
    }

    public function verifikasi(Request $request, Pembayaran $pembayaran)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak',
            'keterangan' => 'nullable|string',
        ]);

        $pembayaran->update([
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);

        // jika diterima → update penagihan
        if ($request->status === 'diterima') {
            $pembayaran->penagihan->update([
                'status' => 'lunas',
            ]);
        }

        return back()->with('success', 'Pembayaran berhasil diverifikasi');
    }

    public function update(Request $request, $id)
    {
        // Validasi input dari form
        $validated = $request->validate([
            'nama_kapal' => 'required|string|max:255',
            'lokasi_kapal' => 'required|string|max:255',
            'jenis_dokumen' => 'required|string',
            'petugas1' => 'nullable|exists:users,id', // Validasi ID Petugas 1
            'petugas2' => 'nullable|exists:users,id', // Validasi ID Petugas 2
            'petugas3' => 'nullable|exists:users,id', // Validasi ID Petugas 3
        ]);

        // Temukan PengajuanPemeriksaanKapal berdasarkan ID
        $pengajuan = PengajuanPemeriksaanKapal::findOrFail($id);

        // Perbarui data pengajuan
        $pengajuan->update([
            'nama_kapal' => $validated['nama_kapal'],
            'lokasi_kapal' => $validated['lokasi_kapal'],
            'jenis_dokumen' => $validated['jenis_dokumen'],
        ]);

        // Perbarui data petugas dalam pivot table
        $penagihan = $pengajuan->penagihan; // Ambil penagihan terkait

        // Cek apakah petugas ada, baru update
        $petugasIds = [];

        if ($request->has('petugas1') && $request->petugas1) {
            $petugasIds[0] = $request->petugas1;
        }
        if ($request->has('petugas2') && $request->petugas2) {
            $petugasIds[1] = $request->petugas2;
        }
        if ($request->has('petugas3') && $request->petugas3) {
            $petugasIds[2] = $request->petugas3;
        }

        // Update petugas jika ada ID petugas yang valid
        $penagihan->petugas()->sync($petugasIds);

        // Redirect kembali dengan pesan sukses
        return back()->with('success', 'Data berhasil diupdate');
    }

    public function updateStatus(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'status' => 'required|in:Diterima,Ditolak', // Ensure status is either diterima or ditolak
            'keterangan' => 'nullable|string|max:255', // Optional keterangan field
        ]);

        // Find the PengajuanPemeriksaanKapal by ID
        $pengajuan = PengajuanPemeriksaanKapal::findOrFail($id);

        // Only allow updates if the current status is 'menunggu verifikasi'
        if ($pengajuan->status === 'Menunggu Verifikasi') {
            // Update status and keterangan
            $pengajuan->status = $request->status; // Set the new status (diterima or ditolak)
            $pengajuan->keterangan = $request->keterangan ?? ''; // Set the keterangan if provided
            $pengajuan->save(); // Save the updated record

            // Redirect with success message
            return redirect()->back()->with('success', 'Pengajuan berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'Status pengajuan tidak dapat diperbarui.');
    }
}
