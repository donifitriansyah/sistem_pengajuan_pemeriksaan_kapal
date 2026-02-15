<?php

namespace App\Models;

use App\Models\Penagihan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanPemeriksaanKapal extends Model
{
    use HasFactory;

    // Nama tabel (karena tidak sesuai default Laravel)
    protected $table = 'pengajuan_pemeriksaan_kapal';

    // Mass assignment
    protected $fillable = [
        'tgl_estimasi_pemeriksaan',
        'nama_kapal',
        'lokasi_kapal',
        'jenis_dokumen',
        'wilayah_kerja',
        'waktu_kedatangan_kapal',
        'surat_permohonan_dan_dokumen',
        'status',
        'keterangan',
        'kode_bayar',
        'penagihan_id',
        'agenda_surat_pengajuan_id',
        'user_id',
    ];

    protected $casts = [
        'waktu_kedatangan_kapal' => 'datetime',
    ];

    /**
     * Relasi ke Penagihan
     */
    public function penagihan()
    {
        return $this->belongsTo(Penagihan::class);
    }

    // In PengajuanPemeriksaanKapal model
public function penagihanId()
{
    return $this->belongsTo(Penagihan::class, 'penagihan_id');
}


    public function agendaSuratPengajuan()
    {
        return $this->belongsTo(AgendaSuratPengajuan::class, 'agenda_surat_pengajuan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function petugas()
    {
        return $this->hasManyThrough(
            User::class,
            PenagihanPetugas::class,
            'penagihan_id', // Foreign key on PenagihanPetugas table
            'id', // Foreign key on User table
            'id', // Local key on PengajuanPemeriksaanKapal
            'user_id' // Local key on PenagihanPetugas table
        );
    }

    // In PengajuanPemeriksaanKapal model
    public function pembayaran()
    {
        // Ambil penagihan_id dari pengajuan_pemeriksaan_kapal
        return $this->hasOneThrough(Pembayaran::class, Penagihan::class, 'id', 'penagihan_id');
    }
}
