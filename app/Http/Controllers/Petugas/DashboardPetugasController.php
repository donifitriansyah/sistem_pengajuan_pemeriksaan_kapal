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

    public function indexPemeriksa()
    {
        return view('pages.petugas.pemeriksa', [
            'pengajuan' => PengajuanPemeriksaanKapal::with([
                'user',
                'penagihan.pembayaran',
                'penagihan.petugas',  // Add this to load petugas data
                'agendaSuratPengajuan',
            ])
                ->get(),
        ]);
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


    public function verifikasiStatus($id)
    {
        // Find the PengajuanPemeriksaanKapal by ID
        $pengajuan = PengajuanPemeriksaanKapal::findOrFail($id);

        // Only allow verification if the current status is 'menunggu verifikasi'
        if ($pengajuan->status === 'menunggu verifikasi') {
            $pengajuan->status = 'diterima'; // Set the status to 'diterima'
            $pengajuan->save(); // Save the updated status

            return redirect()->back()->with('success', 'Pengajuan berhasil diverifikasi.');
        }

        return redirect()->back()->with('error', 'Status pengajuan tidak dapat diverifikasi.');
    }

    public function tolak(Request $request, $id)
    {
        // Validate the keterangan field
        $request->validate([
            'keterangan' => 'required|string|max:255',
        ]);

        // Find the PengajuanPemeriksaanKapal by ID
        $pengajuan = PengajuanPemeriksaanKapal::findOrFail($id);

        // Only allow rejection if the current status is 'menunggu verifikasi'
        if ($pengajuan->status === 'menunggu verifikasi') {
            $pengajuan->status = 'ditolak'; // Set the status to 'ditolak'
            $pengajuan->keterangan = $request->keterangan; // Save the rejection reason
            $pengajuan->save(); // Save the updated status and keterangan

            return redirect()->back()->with('success', 'Pengajuan berhasil ditolak dengan keterangan: '.$request->keterangan);
        }

        return redirect()->back()->with('error', 'Status pengajuan tidak dapat ditolak.');
    }
}
