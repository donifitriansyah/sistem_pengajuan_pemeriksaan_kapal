<?php

namespace App\Models;

use App\Models\Penagihan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenagihanPetugas extends Model
{
    use HasFactory;

    protected $table = 'penagihan_petugas';

    /**
     * Field yang boleh diisi
     */
    protected $fillable = [
        'penagihan_id',
        'user_id',
    ];

    /* =====================
       RELATIONSHIP
    ===================== */

    /**
     * Pivot milik satu penagihan
     */
    public function penagihan()
    {
        return $this->belongsTo(
            Penagihan::class,
            'penagihan_id'
        );
    }

    /**
     * Pivot milik satu user (petugas)
     */
    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}
