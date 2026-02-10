<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penagihan extends Model
{
    use HasFactory;

    protected $table = 'penagihan';

    /**
     * Field yang boleh diisi mass assignment
     */
    protected $fillable = [
        'pengajuan_id',
        'jenis_tarif',
        'jumlah_petugas',
        'total_tarif',
        'waktu_mulai',
        'waktu_selesai',
    ];

    /**
     * Cast otomatis
     */
    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'total_tarif' => 'decimal:2',
    ];

    /* ======================
       RELATION
    ======================= */

    /**
     * Banyak petugas dalam 1 penagihan
     */
    public function petugas()
    {
        return $this->belongsToMany(
            User::class,
            'penagihan_petugas', // pivot table
            'penagihan_id',
            'user_id'
        )->withTimestamps();
    }

    /**
     * Satu penagihan punya satu pembayaran
     */
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }

    public function pengajuan()
    {
        return $this->hasOne(PengajuanPemeriksaanKapal::class);
    }

    /* ======================
       HELPER METHOD
    ======================= */

    /**
     * Cek apakah sudah lunas
     */
    public function isLunas()
    {
        return $this->pembayaran &&
               $this->pembayaran->status === 'diterima';
    }

    /**
     * Format rupiah
     */
    public function getTotalRupiahAttribute()
    {
        return 'Rp '.number_format($this->total_tarif, 0, ',', '.');
    }

    public function getStatusBayarAttribute()
    {
        if (! $this->pembayaran) {
            return 'belum_bayar';
        }

        return $this->pembayaran->status;
        // menunggu | diterima | ditolak
    }
}
