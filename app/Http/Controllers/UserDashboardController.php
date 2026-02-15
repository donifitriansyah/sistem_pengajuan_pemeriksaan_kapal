<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Penagihan;
use App\Models\PengajuanPemeriksaanKapal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function cekInvoice($kodeBayar)
    {
        $invoice = PengajuanPemeriksaanKapal::where('kode_bayar', $kodeBayar)->first();

        if (! $invoice) {
            return response()->json(['error' => 'Kode Bayar tidak ditemukan.'], 404);
        }

        // Ambil penagihan_id untuk mencari pembayaran
        $penagihan_id = $invoice->penagihan_id;
        $pembayaran = Pembayaran::where('penagihan_id', $penagihan_id)->first();

        if (! $pembayaran) {
            return response()->json(['error' => 'Data pembayaran tidak ditemukan.'], 404);
        }

        $statusPembayaran = $pembayaran->status;

        // Menyusun URL untuk verifikasi invoice
        $verifyUrl = route('invoice.verify', $penagihan_id);

        return response()->json([
            'nama_kapal' => $invoice->nama_kapal,
            'jenis_dokumen' => $invoice->jenis_dokumen,
            'lokasi_kapal' => $invoice->lokasi_kapal,
            'wilayah_kerja' => $invoice->wilayah_kerja,
            'penagihan_id' => $invoice->penagihan_id,
            'status_pembayaran' => $statusPembayaran,
            'verify_url' => $verifyUrl, // Kirimkan URL untuk QR Code
        ]);
    }

    public function index()
    {
        // Ambil pengajuan milik user login
        $pengajuan = PengajuanPemeriksaanKapal::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('dashboard', compact('pengajuan'));
    }

    public function update(Request $request, $id)
    {
        // Find the PengajuanPemeriksaanKapal by ID
        $pengajuan = PengajuanPemeriksaanKapal::findOrFail($id);

        // 1. Validasi
        $request->validate([
            'tgl_estimasi_pemeriksaan' => 'required|date',  // Tanggal is required and should be a valid date
            'nama_kapal' => 'required|string|max:255',
            'lokasi_kapal' => 'required|string|max:255',
            'jenis_dokumen' => 'required|in:PHQC,SSCEC,COP,P3K',
            'wilayah_kerja' => 'required|in:Dwikora,Kijing,Padang Tikar,Teluk Batang,Ketapang,Kendawangan',  // Wilayah is required
            'surat_permohonan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',  // Optional file upload
            'waktu_kedatangan_kapal' => 'required|date',  // Time format validation
            'status' => 'nullable|string|in:Menunggu Verifikasi,Diterima,Ditolak',  // Optional status
            'keterangan' => 'nullable|string',  // Optional keterangan
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

        // 3. Generate kode bayar (anti duplikat) - Only if status is updated
        if (! $pengajuan->kode_bayar) { // Check if kode_bayar is not already generated
            do {
                $angkaRandom = rand(1000000, 9999999);
                $kodeBayar = $prefix.'-'.$angkaRandom;
            } while (PengajuanPemeriksaanKapal::where('kode_bayar', $kodeBayar)->exists());
            $pengajuan->kode_bayar = $kodeBayar;
        }

        // 4. Upload file - Only if new file is uploaded
        if ($request->hasFile('surat_permohonan')) {
            $file = $request->file('surat_permohonan');
            $fileName = time().'_'.$file->getClientOriginalName();
            $path = $file->storeAs('surat_permohonan', $fileName, 'public');
            $pengajuan->surat_permohonan_dan_dokumen = $path;
        }

        // 5. Parse the waktu_kedatangan_kapal to store in the database
        $waktuKedatanganKapal = \Carbon\Carbon::parse($request->waktu_kedatangan_kapal)->format('H:i');  // Store just the time in 'H:i' format
        $pengajuan->waktu_kedatangan_kapal = $waktuKedatanganKapal;

        // 6. Determine the 'status' value, default to 'menunggu verifikasi' if not provided
        $pengajuan->status = $request->status ?? 'Menunggu Verifikasi';

        // 7. If the status is 'ditolak', reset keterangan
        if ($pengajuan->status === 'Menunggu Verifikasi') {
            $pengajuan->keterangan = '';  // Clear keterangan if the status is 'menunggu verifikasi'
        } else {
            // Otherwise, set keterangan if it's provided by the user
            $pengajuan->keterangan = $request->keterangan ?? $pengajuan->keterangan;
        }

        // 8. Save the updated data
        $pengajuan->save();

        // 9. Redirect with success message
        return redirect()->back()->with('success', 'Pengajuan berhasil diperbarui.');
    }

    public function store(Request $request, $penagihanId)
    {
        $request->validate([
            'bukti_bayar' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $path = $request->file('bukti_bayar')
            ->store('bukti_pembayaran', 'public');

        Pembayaran::updateOrCreate(
            ['penagihan_id' => $penagihanId],
            [
                'file' => $path,
                'tanggal_bayar' => Carbon::now(),
                'status' => 'menunggu',
                'keterangan' => null,
            ]
        );

        return back()->with('success', 'Bukti pembayaran berhasil dikirim');
    }

    // public function show(Penagihan $penagihan)
    // {
    //     $penagihan->load([
    //         'pengajuan.user',
    //         'pengajuan.agendaSuratPengajuan',
    //         'pembayaran',
    //     ]);

    //     return view('pages.user.invoice.print', compact('penagihan'));
    // }

    // public function show(Penagihan $penagihan)
    // {
    //     // Load necessary relations
    //     $penagihan->load([
    //         'pengajuan.user',
    //         'pengajuan.agendaSuratPengajuan',
    //         'pembayaran',
    //     ]);

    //     // Define base costs
    //     $transportasi = 170000; // Transport fee (per person)
    //     $uang_harian = 150000;  // Daily allowance (per person)
    //     $penginapan = 60000;    // Accommodation fee (per person)

    //     // Get the number of petugas (staff)
    //     $jumlah_petugas = $penagihan->jumlah_petugas;

    //     // Get the jenis_tarif (tariff type) from the database
    //     $jenis_tarif = $penagihan->jenis_tarif;

    //     // Calculate the duration in days between the start and end dates (considering both date and time)
    //     $waktu_mulai = \Carbon\Carbon::parse($penagihan->waktu_mulai);
    //     $waktu_selesai = \Carbon\Carbon::parse($penagihan->waktu_selesai);

    //     // Calculate the difference in days and round up if there is any remaining time
    //     // Using startOfDay ensures we only count full days
    //     $days_difference = $waktu_mulai->startOfDay()
    //         ->diffInDays($waktu_selesai->startOfDay()) + 1; // Add 1 to count the last day as a full day

    //     // Initialize total amounts per petugas (staff)
    //     $total_transportasi_per_petugas = $transportasi;
    //     $total_uang_harian_per_petugas = 0;
    //     $total_penginapan_per_petugas = 0;

    //     // Define a mapping for the jenis_tarif values
    //     $jenisTarifMapping = [
    //         '170000' => 'Dalam Kota (< 8 Jam)',
    //         '320000' => 'Dalam Kota (> 8 Jam)',
    //         '380000' => 'Luar Kota',
    //     ];

    //     // Get human-readable name for jenis_tarif
    //     $jenis_tarif_name = isset($jenisTarifMapping[$jenis_tarif])
    //         ? $jenisTarifMapping[$jenis_tarif]
    //         : 'Unknown'; // Default to 'Unknown' if not found

    //     // Check jenis_tarif to calculate amounts accordingly
    //     if ($jenis_tarif == '170000') {
    //         // For "Dalam Kota (< 8 Jam)", no daily allowance or accommodation
    //         $total_uang_harian_per_petugas = 0;  // No daily allowance for this case
    //         $total_penginapan_per_petugas = 0;  // No accommodation for this case
    //     } elseif ($jenis_tarif == '320000') {
    //         // For "Dalam Kota (> 8 Jam)", include daily allowance but no accommodation
    //         $total_uang_harian_per_petugas = $uang_harian;  // Uang harian for each petugas
    //         $total_penginapan_per_petugas = 0;  // No accommodation for this case
    //     } elseif ($jenis_tarif == '380000') {
    //         // For "Luar Kota", include both daily allowance and accommodation
    //         $total_uang_harian_per_petugas = $uang_harian;  // Uang harian for each petugas
    //         $total_penginapan_per_petugas = $penginapan;  // Penginapan for each petugas
    //     }

    //     // Calculate the total costs per day and per petugas
    //     $total_per_petugas_per_day = $total_transportasi_per_petugas + $total_uang_harian_per_petugas + $total_penginapan_per_petugas;

    //     // Multiply by the number of petugas and days
    //     $total_transportasi = $total_transportasi_per_petugas * $jumlah_petugas;
    //     $total_uang_harian = $total_uang_harian_per_petugas * $jumlah_petugas;
    //     $total_penginapan = $total_penginapan_per_petugas * $jumlah_petugas;
    //     $total = $total_per_petugas_per_day * $jumlah_petugas * $days_difference;

    //     // Fetch the stored total_tarif
    //     $total_tarif = $penagihan->total_tarif;

    //     // Pass the values to the view
    //     return view('pages.user.invoice.print', compact(
    //         'penagihan', 'waktu_mulai', 'waktu_selesai', 'days_difference',
    //         'total_transportasi', 'total_uang_harian', 'total_penginapan', 'total',
    //         'total_transportasi_per_petugas', 'total_uang_harian_per_petugas', 'total_penginapan_per_petugas', 'jumlah_petugas',
    //         'total_tarif', 'jenis_tarif_name' // Pass the total_tarif and jenis_tarif_name to be displayed in the view
    //     ));
    // }

    public function show(Penagihan $penagihan)
    {
        $penagihan->load([
            'pengajuan.user',
            'pengajuan.agendaSuratPengajuan',
            'pembayaran',
        ]);

        // =========================
        // TARIF DASAR
        // =========================
        $transportasi = 170000;
        $uang_harian_dalam_kota = 150000;
        $uang_harian_luar_kota = 380000;
        $penginapan = 60000;

        $jumlah_petugas = $penagihan->jumlah_petugas;
        $jenis_tarif = $penagihan->jenis_tarif;

        // =========================
        // HITUNG DURASI HARI
        // =========================
        $waktu_mulai = \Carbon\Carbon::parse($penagihan->waktu_mulai);
        $waktu_selesai = \Carbon\Carbon::parse($penagihan->waktu_selesai);

        $days_difference = $waktu_mulai->startOfDay()
            ->diffInDays($waktu_selesai->startOfDay()) + 1;

        // =========================
        // INIT NILAI
        // =========================
        $total_transportasi_per_petugas = 0;
        $total_uang_harian_per_petugas = 0;
        $total_penginapan_per_petugas = 0;

        // =========================
        // JENIS TARIF
        // =========================
        $jenisTarifMapping = [
            '170000' => 'Dalam Kota (< 8 Jam)',
            '320000' => 'Dalam Kota (> 8 Jam)',
            '380000' => 'Luar Kota',
        ];

        $jenis_tarif_name = $jenisTarifMapping[$jenis_tarif] ?? 'Unknown';

        // =========================
        // LOGIKA TARIF FINAL
        // =========================
        if ($jenis_tarif == '170000') {

            // Dalam Kota < 8 Jam
            $total_transportasi_per_petugas = $transportasi;

        } elseif ($jenis_tarif == '320000') {

            // Dalam Kota > 8 Jam
            $total_transportasi_per_petugas = $transportasi;
            $total_uang_harian_per_petugas = $uang_harian_dalam_kota;

        } elseif ($jenis_tarif == '380000') {

            // ✅ LUAR KOTA (REVISI FINAL)
            $total_uang_harian_per_petugas = $uang_harian_luar_kota;
        }

        // =========================
        // HITUNG TOTAL
        // =========================
        $total_per_petugas_per_day =
            $total_transportasi_per_petugas +
            $total_uang_harian_per_petugas +
            $total_penginapan_per_petugas;

        $total_transportasi =
            $total_transportasi_per_petugas * $jumlah_petugas * $days_difference;

        $total_uang_harian =
            $total_uang_harian_per_petugas * $jumlah_petugas * $days_difference;

        $total_penginapan =
            $total_penginapan_per_petugas * $jumlah_petugas * $days_difference;

        $total =
            $total_per_petugas_per_day * $jumlah_petugas * $days_difference;

        $total_tarif = $penagihan->total_tarif;

        return view('pages.user.invoice.print', compact(
            'penagihan',
            'waktu_mulai',
            'waktu_selesai',
            'days_difference',
            'jumlah_petugas',
            'jenis_tarif_name',
            'total_transportasi_per_petugas',
            'total_uang_harian_per_petugas',
            'total_penginapan_per_petugas',
            'total_transportasi',
            'total_uang_harian',
            'total_penginapan',
            'total',
            'total_tarif'
        ));
    }

    public function verify(Penagihan $penagihan)
    {
        abort_if(
            $penagihan->status_bayar !== 'diterima',
            403,
            'Invoice belum lunas atau tidak valid'
        );

        return view('pages.user.invoice.verify', compact('penagihan'));
    }
}
