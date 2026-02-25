<?php

namespace App\Exports;

use App\Models\PengajuanPemeriksaanKapal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PengajuanExport implements FromCollection, WithHeadings
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function collection()
    {
        return PengajuanPemeriksaanKapal::with('pembayaran')
            ->where('user_id', $this->userId)
            ->get()
            ->map(function ($item) {

                $statusPembayaran = $item->pembayaran?->status ?? 'Belum Ada Pembayaran';

                return [
                    $item->tgl_estimasi_pemeriksaan,
                    $item->nama_kapal,
                    $item->lokasi_kapal,
                    $item->jenis_dokumen,
                    $item->wilayah_kerja,
                    $item->kode_bayar,
                    $item->status,
                    $item->keterangan,
                    $statusPembayaran,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Tanggal Estimasi Pemeriksaan',
            'Nama Kapal',
            'Lokasi Kapal',
            'Jenis Dokumen',
            'Wilayah Kerja',
            'Kode Bayar',
            'Status Pengajuan',
            'Keterangan',
            'Status Pembayaran',
        ];
    }
}
