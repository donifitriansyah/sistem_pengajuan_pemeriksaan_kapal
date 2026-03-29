<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'penagihan_id',
        'file',
        'tanggal_bayar',
        'status',
        'keterangan',
    ];

    public function penagihan()
    {
        return $this->belongsTo(Penagihan::class);
    }

}
