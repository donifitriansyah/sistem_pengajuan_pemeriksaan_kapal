<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWaktuKedatanganKapalToPengajuanPemeriksaanKapalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengajuan_pemeriksaan_kapal', function (Blueprint $table) {
            $table->time('waktu_kedatangan_kapal')->nullable();  // Add 'time' type field
        });
    }

    public function down()
    {
        Schema::table('pengajuan_pemeriksaan_kapal', function (Blueprint $table) {
            $table->dropColumn('waktu_kedatangan_kapal');
        });
    }
}
