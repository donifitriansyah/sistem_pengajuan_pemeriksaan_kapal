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
    public function index()
    {
        // Ambil pengajuan milik user login
        $pengajuan = PengajuanPemeriksaanKapal::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('dashboard', compact('pengajuan'));
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

    public function show(Penagihan $penagihan)
    {
        // Load necessary relations
        $penagihan->load([
            'pengajuan.user',
            'pengajuan.agendaSuratPengajuan',
            'pembayaran',
        ]);

        // Define base costs
        $transportasi = 170000; // Transport fee (per person)
        $uang_harian = 150000;  // Daily allowance (per person)
        $penginapan = 60000;    // Accommodation fee (per person)

        // Get the number of petugas (staff)
        $jumlah_petugas = $penagihan->jumlah_petugas;

        // Get the jenis_tarif (tariff type) from the database
        $jenis_tarif = $penagihan->jenis_tarif;

        // Calculate the duration in days between the start and end dates (considering both date and time)
        $waktu_mulai = \Carbon\Carbon::parse($penagihan->waktu_mulai);
        $waktu_selesai = \Carbon\Carbon::parse($penagihan->waktu_selesai);

        // Calculate the difference in days and round up if there is any remaining time
        // Using startOfDay ensures we only count full days
        $days_difference = $waktu_mulai->startOfDay()
            ->diffInDays($waktu_selesai->startOfDay()) + 1; // Add 1 to count the last day as a full day

        // Initialize total amounts per petugas (staff)
        $total_transportasi_per_petugas = $transportasi;
        $total_uang_harian_per_petugas = 0;
        $total_penginapan_per_petugas = 0;

        // Define a mapping for the jenis_tarif values
        $jenisTarifMapping = [
            '170000' => 'Dalam Kota (< 8 Jam)',
            '320000' => 'Dalam Kota (> 8 Jam)',
            '380000' => 'Luar Kota',
        ];

        // Get human-readable name for jenis_tarif
        $jenis_tarif_name = isset($jenisTarifMapping[$jenis_tarif])
            ? $jenisTarifMapping[$jenis_tarif]
            : 'Unknown'; // Default to 'Unknown' if not found

        // Check jenis_tarif to calculate amounts accordingly
        if ($jenis_tarif == '170000') {
            // For "Dalam Kota (< 8 Jam)", no daily allowance or accommodation
            $total_uang_harian_per_petugas = 0;  // No daily allowance for this case
            $total_penginapan_per_petugas = 0;  // No accommodation for this case
        } elseif ($jenis_tarif == '320000') {
            // For "Dalam Kota (> 8 Jam)", include daily allowance but no accommodation
            $total_uang_harian_per_petugas = $uang_harian;  // Uang harian for each petugas
            $total_penginapan_per_petugas = 0;  // No accommodation for this case
        } elseif ($jenis_tarif == '380000') {
            // For "Luar Kota", include both daily allowance and accommodation
            $total_uang_harian_per_petugas = $uang_harian;  // Uang harian for each petugas
            $total_penginapan_per_petugas = $penginapan;  // Penginapan for each petugas
        }

        // Calculate the total costs per day and per petugas
        $total_per_petugas_per_day = $total_transportasi_per_petugas + $total_uang_harian_per_petugas + $total_penginapan_per_petugas;

        // Multiply by the number of petugas and days
        $total_transportasi = $total_transportasi_per_petugas * $jumlah_petugas;
        $total_uang_harian = $total_uang_harian_per_petugas * $jumlah_petugas;
        $total_penginapan = $total_penginapan_per_petugas * $jumlah_petugas;
        $total = $total_per_petugas_per_day * $jumlah_petugas * $days_difference;

        // Fetch the stored total_tarif
        $total_tarif = $penagihan->total_tarif;

        // Pass the values to the view
        return view('pages.user.invoice.print', compact(
            'penagihan', 'waktu_mulai', 'waktu_selesai', 'days_difference',
            'total_transportasi', 'total_uang_harian', 'total_penginapan', 'total',
            'total_transportasi_per_petugas', 'total_uang_harian_per_petugas', 'total_penginapan_per_petugas', 'jumlah_petugas',
            'total_tarif', 'jenis_tarif_name' // Pass the total_tarif and jenis_tarif_name to be displayed in the view
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
