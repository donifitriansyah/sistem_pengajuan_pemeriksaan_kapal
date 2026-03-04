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

    public function destroy($id)
    {
        $pengajuan = PengajuanPemeriksaanKapal::findOrFail($id);

        $pengajuan->delete();

        return redirect()
            ->route('petugas.dashboard')
            ->with('success', 'Data pengajuan berhasil dihapus.');
    }

    public function indexPengajuan()
    {
        $user = auth()->user();
        $wilayah_kerja = $user->wilayah_kerja; // <-- tambahkan ini

        $pengajuan = PengajuanPemeriksaanKapal::with([
            'user',
            'penagihan',
            'agendaSuratPengajuan',
        ])
            ->where('wilayah_kerja', $wilayah_kerja) // lebih efisien
            ->get();

        $petugas = User::where('wilayah_kerja', $wilayah_kerja)
            ->whereNotIn('role', ['admin', 'user'])
            ->get();

        return view('pages.petugas.pengajuan', compact('pengajuan', 'petugas'));
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
        $user = auth()->user();

        $pengajuan = PengajuanPemeriksaanKapal::with([
            'user',
            'penagihan.pembayaran',
            'agendaSuratPengajuan',
        ])
            ->where('wilayah_kerja', $user->wilayah_kerja)
            ->latest() // sama dengan orderBy('created_at', 'desc')
            ->get();

        return view('pages.petugas.pembayaran', [
            'pengajuan' => $pengajuan,
        ]);
    }

    public function indexPemeriksa()
    {
        $user = auth()->user();

        $pengajuan = PengajuanPemeriksaanKapal::with([
            'user',
            'penagihan.pembayaran',
            'penagihan.petugas',
            'agendaSuratPengajuan',
        ])
            ->where('wilayah_kerja', $user->wilayah_kerja)
            ->latest() // tampil terbaru dulu
            ->get();

        $petugas = User::where('wilayah_kerja', $user->wilayah_kerja)
            ->whereNotIn('role', ['admin', 'user'])
            ->get();

        return view('pages.petugas.pemeriksa', [
            'pengajuan' => $pengajuan,
            'petugas' => $petugas,
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
            'status' => 'required|in:Diterima,Ditolak',

            'keterangan' => 'nullable|string|max:255',

            'nomor_surat_pengajuan' => 'nullable|required_if:status,Diterima|string|max:255',

            'tanggal_surat' => 'nullable|required_if:status,Diterima|date',
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
        $pengajuan = PengajuanPemeriksaanKapal::findOrFail($id);

        $currentYear = date('Y');
        $wilker = strtolower(auth()->user()->wilayah_kerja);

        /*
        |--------------------------------------------------------------------------
        | Mapping Kode Wilker
        |--------------------------------------------------------------------------
        */
        $wilkerMapping = [
            'dwikora' => [
                'kode' => 'C.X.1.11',
                'start' => 265,
            ],
            'kendawangan' => [
                'kode' => 'C.X.1.9',
                'start' => 1,
            ],
            'ketapang' => [
                'kode' => 'C.X.1.8',
                'start' => 40,
            ],
            'kijing' => [
                'kode' => 'C.X.1.12',
                'start' => 42,
            ],
            'padang tikar' => [
                'kode' => 'C.X.1.10',
                'start' => 1,
            ],
        ];

        // Default jika tidak ada
        $wilkerCode = $wilkerMapping[$wilker]['kode'] ?? 'C.X.1.11';
        $startNumber = $wilkerMapping[$wilker]['start'] ?? 1;

        /*
        |--------------------------------------------------------------------------
        | Generate Nomor Surat Masuk
        |--------------------------------------------------------------------------
        */
        $nomorSuratMasuk = $this->generateNomorSurat(
            'AR.02.01',
            $wilkerCode,
            $currentYear,
            $startNumber,
            'nomor_surat_masuk'
        );

        /*
        |--------------------------------------------------------------------------
        | Generate Nomor Surat Keluar
        |--------------------------------------------------------------------------
        */
        $nomorSuratKeluar = $this->generateNomorSurat(
            'SR.02.04',
            $wilkerCode,
            $currentYear,
            $startNumber,
            'nomor_surat_keluar'
        );

        /*
        |--------------------------------------------------------------------------
        | Simpan Agenda
        |--------------------------------------------------------------------------
        */
        $agenda = AgendaSuratPengajuan::create([
            'nomor_surat_pengajuan' => $request->input('nomor_surat_pengajuan'),
            'nomor_surat_masuk' => $nomorSuratMasuk,
            'nomor_surat_keluar' => $nomorSuratKeluar,
            'tanggal_surat' => $request->input('tanggal_surat'),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Update Pengajuan
        |--------------------------------------------------------------------------
        */
        $pengajuan->update([
            'agenda_surat_pengajuan_id' => $agenda->id,
        ]);

        return back()->with('success', 'Pengajuan berhasil diarsipkan.');
    }

    private function generateNomorSurat($prefix, $wilkerCode, $year, $startNumber, $field)
    {
        $last = AgendaSuratPengajuan::where($field, 'like', "$prefix/$wilkerCode/%/$year")
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $lastNumber = (int) explode('/', $last->$field)[2];
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = $startNumber;
        }

        return "$prefix/$wilkerCode/$nextNumber/$year";
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
