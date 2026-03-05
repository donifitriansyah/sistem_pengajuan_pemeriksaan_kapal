<?php

namespace App\Exports;

use App\Models\PengajuanPemeriksaanKapal;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VerifikasiPembayaranLunasExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected $mulai;

    protected $selesai;

    public function __construct($mulai = null, $selesai = null)
    {
        $this->mulai = $mulai;
        $this->selesai = $selesai;
    }

    /*
    |--------------------------------------------------------------------------
    | COLLECTION
    |--------------------------------------------------------------------------
    | Ambil data hanya:
    | - Sesuai wilker login
    | - Sesuai range tanggal (jika ada)
    | - Punya penagihan
    | - Status pembayaran = diterima (LUNAS)
    */
    public function collection()
    {
        $user = auth()->user();

        $query = PengajuanPemeriksaanKapal::with([
            'user',
            'penagihan.pembayaran',
            'agendaSuratPengajuan',
        ]);

        if ($user->wilayah_kerja) {
            $query->where('wilayah_kerja', $user->wilayah_kerja);
        }

        // FILTER TANGGAL (lebih aman pakai whereDate)
        if ($this->mulai) {
            $query->whereDate('tgl_estimasi_pemeriksaan', '>=', $this->mulai);
        }

        if ($this->selesai) {
            $query->whereDate('tgl_estimasi_pemeriksaan', '<=', $this->selesai);
        }

        // WAJIB punya penagihan
        $query->whereHas('penagihan');

        // 🔥 HANYA BELUM BAYAR (tidak punya pembayaran)
        $query->whereDoesntHave('penagihan.pembayaran');

        return $query->get();
    }

    /*
    |--------------------------------------------------------------------------
    | HEADER
    |--------------------------------------------------------------------------
    */
    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Kapal',
            'Perusahaan',
            'Lokasi',
            'Jenis Dokumen',
            'Nomor Surat',
            'Kode Bayar',
            'Status Pembayaran',
            'Total Tarif (Rp)',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MAPPING DATA
    |--------------------------------------------------------------------------
    */
    public function map($item): array
    {
        return [
            Carbon::parse($item->tgl_estimasi_pemeriksaan)->format('d-m-Y'),
            $item->nama_kapal,
            $item->user->nama_perusahaan ?? '-',
            $item->lokasi_kapal,
            $item->jenis_dokumen,
            $item->agendaSuratPengajuan->nomor_surat_keluar ?? '-',
            $item->kode_bayar,
            'Lunas',
            $item->penagihan->total_tarif ?? 0,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | STYLE EXCEL
    |--------------------------------------------------------------------------
    */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Header bold
        ];
    }
}
