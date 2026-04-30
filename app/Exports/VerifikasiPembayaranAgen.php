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

class VerifikasiPembayaranAgen implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected $mulai;

    protected $selesai;

    protected $tahun;

    protected $bulan;

    protected $perusahaan;

    protected $jenis;

    public function __construct(
        $mulai = null,
        $selesai = null,
        $tahun = null,
        $bulan = null,
        $perusahaan = null,
        $jenis = null
    ) {
        $this->mulai = $mulai;
        $this->selesai = $selesai;

        $this->tahun = $tahun;
        $this->bulan = $bulan;
        $this->perusahaan = $perusahaan;
        $this->jenis = $jenis;
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

        // FILTER WILKER
        if ($user->wilayah_kerja) {
            $query->where('wilayah_kerja', $user->wilayah_kerja);
        }

        // FILTER TANGGAL
        if ($this->mulai) {
            $query->whereDate('tgl_estimasi_pemeriksaan', '>=', $this->mulai);
        }

        if ($this->selesai) {
            $query->whereDate('tgl_estimasi_pemeriksaan', '<=', $this->selesai);
        }

        // FILTER TAHUN
        if ($this->tahun) {
            $query->whereYear('tgl_estimasi_pemeriksaan', $this->tahun);
        }

        // FILTER BULAN
        if ($this->bulan) {
            $query->whereMonth('tgl_estimasi_pemeriksaan', $this->bulan);
        }

        // FILTER PERUSAHAAN
        if ($this->perusahaan) {
            $query->whereHas('user', function ($q) {
                $q->where('nama_perusahaan', $this->perusahaan);
            });
        }

        // FILTER JENIS DOKUMEN
        if ($this->jenis) {
            $query->where('jenis_dokumen', $this->jenis);
        }

        // HANYA DIFASILITASI AGEN
        $query->whereHas('penagihan', function ($q) {
    $q->where('difasilitasi_agen', 1);
});

        return $query->latest()->get();
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
            'Difasilitasi Agen',
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
