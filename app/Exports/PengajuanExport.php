<?php
// app/Exports/PengajuanExport.php

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
        return PengajuanPemeriksaanKapal::where('user_id', $this->userId)
            ->get([
                'tgl_estimasi_pemeriksaan',
                'nama_kapal',
                'lokasi_kapal',
                'jenis_dokumen',
                'wilayah_kerja',
                'surat_permohonan_dan_dokumen',
                'kode_bayar',
                'status',
                'keterangan',
                'penagihan_id', // Pastikan penagihan_id ada di sini
            ])->map(function ($item) {
                // Cek jika penagihan ada dan ambil status pembayaran
                $statusPembayaran = $item->penagihan && $item->penagihan->pembayaran
                    ? $item->penagihan->pembayaran->status
                    : 'Belum Ada Pembayaran'; // Jika tidak ada pembayaran, tampilkan 'Belum Ada Pembayaran'

                $item->status_bayar = $statusPembayaran; // Menambahkan status pembayaran
                return $item;
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
            'Surat Permohonan dan Dokumen',
            'Kode Bayar',
            'Status Pengajuan',
            'Keterangan',
            'Status Pembayaran', // Kolom baru untuk status pembayaran
        ];
    }
}
