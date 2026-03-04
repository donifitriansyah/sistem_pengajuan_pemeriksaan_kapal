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
        $user = auth()->user();

        if (! $user || ! $user->wilayah_kerja) {
            abort(403, 'Wilayah kerja tidak ditemukan.');
        }

        $wilayah_kerja = $user->wilayah_kerja;

        $pengajuan = PengajuanPemeriksaanKapal::with([
            'user',
            'agendaSuratPengajuan',
        ])
            ->where('wilayah_kerja', $wilayah_kerja)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'Ditolak');
            })
            ->whereNotNull('agenda_surat_pengajuan_id')
            ->doesntHave('penagihan')
            ->latest()
            ->get();

        $petugas = User::where('wilayah_kerja', $wilayah_kerja)
            ->whereNotIn('role', ['admin', 'user'])
            ->get();

        $penagihanData = Penagihan::with('petugas')
            ->whereHas('petugas', function ($query) use ($wilayah_kerja) {
                $query->where('wilayah_kerja', $wilayah_kerja);
            })
            ->latest()
            ->get();

        return view('pages.petugas.dashboard', [
            'pengajuan' => $pengajuan,
            'petugas' => $petugas,
            'penagihanData' => $penagihanData,
        ]);
    }

    public function destroy($id)
    {
        $pengajuan = PengajuanPemeriksaanKapal::findOrFail($id);

        $pengajuan->delete();

        return redirect()
            ->route('arsiparis.verifikasi')
            ->with('success', 'Data pengajuan berhasil dihapus.');
    }

    public function indexPengajuan()
    {
        $user = auth()->user();

        // Guard tambahan
        if (! $user || ! $user->wilayah_kerja) {
            abort(403, 'Wilayah kerja tidak ditemukan.');
        }

        $pengajuan = PengajuanPemeriksaanKapal::with([
            'user',
            'penagihan',
            'agendaSuratPengajuan',
        ])
            ->where('wilayah_kerja', $user->wilayah_kerja)

            // 🔥 tampilkan semua tanpa filter tambahan
            ->orderByDesc('created_at')

            ->get();

        $petugas = User::where('wilayah_kerja', $user->wilayah_kerja)
            ->whereNotIn('role', ['admin', 'user'])
            ->get();

        return view('pages.petugas.pengajuan', compact('pengajuan', 'petugas'));
    }

    public function indexPembayaran()
    {
        $user = auth()->user();

        $pengajuan = PengajuanPemeriksaanKapal::with([
            'user',
            'penagihan.pembayaran',
            'agendaSuratPengajuan',
        ])
            ->where('wilayah_kerja', $user->wilayah_kerja)

            // hanya yang menunggu verifikasi
            ->whereHas('penagihan')
            ->where(function ($query) {
                $query->whereDoesntHave('penagihan.pembayaran')
                    ->orWhereHas('penagihan.pembayaran', function ($q) {
                        $q->where('status', 'menunggu');
                    });
            })
            ->latest()
            ->get();

        return view('pages.petugas.pembayaran', [
            'pengajuan' => $pengajuan,
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

            // hanya yang menunggu verifikasi
            ->whereHas('penagihan')
            ->where(function ($query) {
                $query->whereDoesntHave('penagihan.pembayaran')
                    ->orWhereHas('penagihan.pembayaran', function ($q) {
                        $q->where('status', 'menunggu');
                    });
            })
            ->latest()
            ->get();

        return view('pages.petugas.pembayaran', [
            'pengajuan' => $pengajuan,
        ]);
    }

    public function indexPemeriksa()
    {
        $user = auth()->user();

        if (! $user || ! $user->wilayah_kerja) {
            abort(403, 'Wilayah kerja tidak ditemukan.');
        }

        $pengajuan = PengajuanPemeriksaanKapal::with([
            'user',
            'penagihan.pembayaran',
            'penagihan.petugas',
            'agendaSuratPengajuan',
        ])
            ->where('wilayah_kerja', $user->wilayah_kerja)

            // 🔥 hanya yang SUDAH ADA penagihan
            ->has('penagihan')

            ->orderByDesc('created_at')
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
        $user = auth()->user();

        if (! $user || ! $user->wilayah_kerja) {
            abort(403, 'Wilayah kerja tidak ditemukan.');
        }

        $wilayah_kerja = $user->wilayah_kerja;

        $pengajuan = PengajuanPemeriksaanKapal::with([
            'user',
            'agendaSuratPengajuan',
        ])
            ->where('wilayah_kerja', $wilayah_kerja)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'Ditolak');
            })
            ->whereNotNull('agenda_surat_pengajuan_id')
            ->doesntHave('penagihan')
            ->latest()
            ->get();

        $petugas = User::where('wilayah_kerja', $wilayah_kerja)
            ->whereNotIn('role', ['admin', 'user'])
            ->get();

        $penagihanData = Penagihan::with('petugas')
            ->whereHas('petugas', function ($query) use ($wilayah_kerja) {
                $query->where('wilayah_kerja', $wilayah_kerja);
            })
            ->latest()
            ->get();

        return view('pages.petugas.dashboard', [
            'pengajuan' => $pengajuan,
            'petugas' => $petugas,
            'penagihanData' => $penagihanData,
        ]);
    }
}
