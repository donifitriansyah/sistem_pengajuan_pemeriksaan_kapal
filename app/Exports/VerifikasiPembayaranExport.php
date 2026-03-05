<?php

namespace App\Exports;

use App\Models\PengajuanPemeriksaanKapal;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VerifikasiPembayaranExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected $mulai;

    protected $selesai;

    protected $status;

    public function __construct($mulai, $selesai, $status)
    {
        $this->mulai = $mulai;
        $this->selesai = $selesai;
        $this->status = $status;
    }

    public function collection()
    {
        $user = auth()->user();

        $query = PengajuanPemeriksaanKapal::with([
            'user',
            'penagihan',
            'agendaSuratPengajuan',
        ]);

        // Filter wilayah kerja
        if ($user->wilayah_kerja) {
            $query->where('wilayah_kerja', $user->wilayah_kerja);
        }

        // Filter tanggal
        if ($this->mulai) {
            $query->whereDate('tgl_estimasi_pemeriksaan', '>=', $this->mulai);
        }

        if ($this->selesai) {
            $query->whereDate('tgl_estimasi_pemeriksaan', '<=', $this->selesai);
        }

        // Wajib ada penagihan
        $query->whereHas('penagihan');

        // 🔥 HANYA BELUM BAYAR
        $query->whereDoesntHave('penagihan.pembayaran');

        return $query->get();
    }

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
            'Total Tarif',
        ];
    }

    public function map($item): array
    {
        $status = 'Belum Bayar';

        if ($item->penagihan && $item->penagihan->pembayaran) {

            $pStatus = $item->penagihan->pembayaran->status;

            if ($pStatus === 'menunggu') {
                $status = 'Menunggu Verifikasi';
            } elseif ($pStatus === 'diterima') {
                $status = 'Lunas';
            } elseif ($pStatus === 'ditolak') {
                $status = 'Ditolak';
            }
        }

        return [
            Carbon::parse($item->tgl_estimasi_pemeriksaan)->format('d-m-Y'),
            $item->nama_kapal,
            $item->user->nama_perusahaan ?? '-',
            $item->lokasi_kapal,
            $item->jenis_dokumen,
            $item->agendaSuratPengajuan->nomor_surat_keluar ?? '-',
            $item->kode_bayar,
            $status,
            $item->penagihan->total_tarif ?? 0,
        ];
    }
}
