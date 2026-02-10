<?php

namespace App\Models;

use App\Models\PengajuanPemeriksaanKapal;
use Illuminate\Database\Eloquent\Model;

class AgendaSuratPengajuan extends Model
{
    protected $table = 'agenda_surat_pengajuan';

    protected $fillable = [

        'nomor_surat_pengajuan',
        'nomor_surat_keluar',
        'nomor_surat_masuk',
        'tanggal_surat',

    ];

    public function pengajuan()
    {
        return $this->hasOne(PengajuanPemeriksaanKapal::class, 'agenda_surat_pengajuan_id');
    }

}

