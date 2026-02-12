<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAndKeteranganToPengajuanPemeriksaanKapalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengajuan_pemeriksaan_kapal', function (Blueprint $table) {
            $table->string('status')->nullable();  // Add 'status' column
            $table->text('keterangan')->nullable();  // Add 'keterangan' column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengajuan_pemeriksaan_kapal', function (Blueprint $table) {
            $table->dropColumn('status');  // Drop 'status' column
            $table->dropColumn('keterangan');  // Drop 'keterangan' column
        });
    }
}
