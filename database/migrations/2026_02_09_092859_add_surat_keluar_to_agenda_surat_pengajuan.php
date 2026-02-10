<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agenda_surat_pengajuan', function (Blueprint $table) {
            $table->string('nomor_surat_keluar')
                ->nullable()
                ->after('nomor_surat_pengajuan');
        });
    }

    public function down(): void
    {
        Schema::table('agenda_surat_pengajuan', function (Blueprint $table) {
            $table->dropColumn('nomor_surat_keluar');
        });
    }
};
