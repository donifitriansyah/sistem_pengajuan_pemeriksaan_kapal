<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\AgendaSuratPengajuan;
use App\Models\Pembayaran;
use App\Models\Penagihan;
use App\Models\PengajuanPemeriksaanKapal;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardPetugasController extends Controller
{
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
        $petugas = User::where('wilayah_kerja', $wilayah_kerja)
            ->whereNotIn('role', ['admin', 'user'])
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
        // Get the logged-in user
        $user = auth()->user();

        // Fetch pengajuan where wilayah_kerja matches the logged-in user's wilayah_kerja
        $pengajuan = PengajuanPemeriksaanKapal::with([
            'user',
            'penagihan',
            'agendaSuratPengajuan',
        ])
            ->whereHas('user', function ($query) use ($user) {
                // Filter based on wilayah_kerja of the logged-in user
                $query->where('wilayah_kerja', $user->wilayah_kerja);
            })
            ->get();

        // Fetch petugas based on wilayah_kerja of the logged-in user
        $petugas = User::where('wilayah_kerja', $wilayah_kerja)
            ->whereNotIn('role', ['admin', 'user'])
            ->get();

        return view('pages.petugas.pengajuan', [
            'pengajuan' => $pengajuan,
            'petugas' => $petugas, // Send filtered petugas to the view
        ]);
    }

    public function indexPembayaran()
    {
        // Get the logged-in user
        $user = auth()->user();

        // Fetch pengajuan where wilayah_kerja matches the logged-in user's wilayah_kerja
        $pengajuan = PengajuanPemeriksaanKapal::with([
            'user',
            'penagihan.pembayaran',
            'agendaSuratPengajuan',
        ])
            ->whereHas('user', function ($query) use ($user) {
                // Filter based on wilayah_kerja of the logged-in user
                $query->where('wilayah_kerja', $user->wilayah_kerja);
            })
            ->get();

        return view('pages.petugas.pembayaran', [
            'pengajuan' => $pengajuan, // Send filtered pengajuan to the view
        ]);
    }
    public function indexPembayaranPetugas()
    {
        // Get the logged-in user
        $user = auth()->user();

        // Fetch pengajuan where wilayah_kerja matches the logged-in user's wilayah_kerja
        $pengajuan = PengajuanPemeriksaanKapal::with([
            'user',
            'penagihan.pembayaran',
            'agendaSuratPengajuan',
        ])
            ->whereHas('user', function ($query) use ($user) {
                // Filter based on wilayah_kerja of the logged-in user
                $query->where('wilayah_kerja', $user->wilayah_kerja);
            })
            ->get();

        return view('pages.petugas.pembayaran', [
            'pengajuan' => $pengajuan, // Send filtered pengajuan to the view
        ]);
    }

    public function indexPemeriksa()
    {
        // Get the logged-in user
        $user = auth()->user();

        // Fetch pengajuan where wilayah_kerja matches the logged-in user's wilayah_kerja
        $pengajuan = PengajuanPemeriksaanKapal::with([
            'user',
            'penagihan.pembayaran',
            'penagihan.petugas',  // Load petugas
            'agendaSuratPengajuan',
        ])
            ->whereHas('user', function ($query) use ($user) {
                // Filter based on wilayah_kerja of the logged-in user
                $query->where('wilayah_kerja', $user->wilayah_kerja);
            })
            ->get();

        // Fetch 'petugas' where wilayah_kerja matches the logged-in user's wilayah_kerja
        $petugas = User::where('wilayah_kerja', $user->wilayah_kerja) // Use $user->wilayah_kerja
            ->whereNotIn('role', ['admin', 'user'])
            ->get();

        return view('pages.petugas.pemeriksa', [
            'pengajuan' => $pengajuan,
            'petugas' => $petugas, // Send filtered petugas to the view
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
            'nomor_surat_pengajuan' => 'required_if:status,Diterima|string|max:255', // Nomor surat hanya diperlukan jika diterima
            'tanggal_surat' => 'required_if:status,Diterima|date', // Tanggal surat hanya diperlukan jika diterima
        ]);

        // Find the PengajuanPemeriksaanKapal by ID
        $pengajuan = PengajuanPemeriksaanKapal::findOrFail($id);

        // Only allow updates if the current status is 'menunggu verifikasi'
        if ($pengajuan->status === 'Menunggu Verifikasi') {
            // Update status and keterangan
            $pengajuan->status = $request->status; // Set the new status (diterima or ditolak)
            $pengajuan->keterangan = $request->keterangan ?? ''; // Set the keterangan if provided
            $pengajuan->save(); // Save the updated record

            // If the status is "Diterima", run arsipkan method to handle surat masuk and keluar
            if ($request->status === 'Diterima') {
                $this->arsipkan($request, $id); // Call the arsipkan method
            }

            // Redirect with success message
            return redirect()->back()->with('success', 'Pengajuan berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'Status pengajuan tidak dapat diperbarui.');
    }

    public function arsipkan(Request $request, $id)
    {
        // Find the PengajuanPemeriksaanKapal record to archive
        $pengajuan = PengajuanPemeriksaanKapal::findOrFail($id);

        // Get the current year
        $currentYear = date('Y');

        // Get the logged-in user's wilker
        $wilker = auth()->user()->wilayah_kerja;

        // Set the wilayah kerja code based on the wilker
        $wilkerCode = '';
        switch ($wilker) {
            case 'Dwikora':
                $wilkerCode = 'C.X.1.11';
                break;
            case 'Kendawangan':
                $wilkerCode = 'C.X.1.9';
                break;
            case 'Ketapang':
                $wilkerCode = 'C.X.1.8';
                break;
            case 'Kijing':
                $wilkerCode = 'C.X.1.12';
                break;
            case 'Padang Tikar':
                $wilkerCode = 'C.X.1.10';
                break;
            default:
                $wilkerCode = 'C.X.1.11'; // Default if no match
                break;
        }

        // Generate the nomor surat masuk otomatis (incrementing logic)
        $lastSuratMasuk = AgendaSuratPengajuan::where('nomor_surat_masuk', 'like', 'AR.02.01/'.$wilkerCode.'/%/'.$currentYear)
            ->orderBy('id', 'desc')
            ->first();

        $suratMasukNumber = $lastSuratMasuk ? (int) explode('/', $lastSuratMasuk->nomor_surat_masuk)[2] + 1 : 1;
        $nomorSuratMasuk = 'AR.02.01/'.$wilkerCode.'/'.$suratMasukNumber.'/'.$currentYear;

        // Generate the nomor surat keluar otomatis (incrementing logic)
        $lastSuratKeluar = AgendaSuratPengajuan::where('nomor_surat_keluar', 'like', 'SR.04.02/'.$wilkerCode.'/%/'.$currentYear)
            ->orderBy('id', 'desc')
            ->first();

        $suratKeluarNumber = $lastSuratKeluar ? (int) explode('/', $lastSuratKeluar->nomor_surat_keluar)[2] + 1 : 1;
        $nomorSuratKeluar = 'SR.04.02/'.$wilkerCode.'/'.$suratKeluarNumber.'/'.$currentYear;

        // Check if the generated nomor_surat_keluar already exists in the database
        $existingSuratKeluar = AgendaSuratPengajuan::where('nomor_surat_keluar', $nomorSuratKeluar)->first();

        // If the nomor_surat_keluar already exists, regenerate it by incrementing the number
        if ($existingSuratKeluar) {
            $suratKeluarNumber++;
            $nomorSuratKeluar = 'SR.04.02/'.$wilkerCode.'/'.$suratKeluarNumber.'/'.$currentYear;
        }

        // Create a new AgendaSuratPengajuan record
        $agenda = AgendaSuratPengajuan::create([
            'nomor_surat_pengajuan' => $request->input('nomor_surat_pengajuan'), // Nomor surat pengajuan diisi manual oleh arsiparis
            'nomor_surat_masuk' => $nomorSuratMasuk,  // Automatically generated and incremented
            'nomor_surat_keluar' => $nomorSuratKeluar, // Automatically generated and ensured unique
            'tanggal_surat' => $request->input('tanggal_surat'), // Tanggal surat dari arsiparis
        ]);

        // Update the PengajuanPemeriksaanKapal record with the new agenda_surat_pengajuan_id
        $pengajuan->update([
            'agenda_surat_pengajuan_id' => $agenda->id, // Link the new AgendaSuratPengajuan
        ]);

        // Redirect back with a success message
        return back()->with('success', 'Pengajuan berhasil diarsipkan.');
    }

    public function indexKeuangan()
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
        $petugas = User::where('wilayah_kerja', $wilayah_kerja)
            ->whereNotIn('role', ['admin', 'user'])
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
}
