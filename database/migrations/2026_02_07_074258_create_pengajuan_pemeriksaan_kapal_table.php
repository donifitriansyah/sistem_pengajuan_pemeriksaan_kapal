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
        Schema::create('pengajuan_pemeriksaan_kapal', function (Blueprint $table) {
            $table->id();

            // Tanggal estimasi pemeriksaan
            $table->date('tgl_estimasi_pemeriksaan');

            // Informasi kapal
            $table->string('nama_kapal');
            $table->string('lokasi_kapal');

            // Dokumen
            $table->string('jenis_dokumen');
            $table->string('wilayah_kerja');

            // File surat & dokumen
            $table->string('surat_permohonan_dan_dokumen');

            // Kode bayar
            $table->string('kode_bayar')->unique();

            // Relasi User
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Relasi Penagihan
            $table->foreignId('penagihan_id')
                  ->nullable()
                  ->constrained('penagihan')
                  ->nullOnDelete();

            // Relasi Agenda Surat
            $table->foreignId('agenda_surat_pengajuan_id')
                  ->nullable()
                  ->constrained('agenda_surat_pengajuan')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_pengajuan_pemeriksaan_kapal');
    }
};
