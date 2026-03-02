<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agenda_surat_pengajuan', function (Blueprint $table) {
            // Drop unique index
            $table->dropUnique(['nomor_surat_pengajuan']);
        });
    }

    public function down(): void
    {
        Schema::table('agenda_surat_pengajuan', function (Blueprint $table) {
            // Tambahkan kembali unique jika rollback
            $table->unique('nomor_surat_pengajuan');
        });
    }
};
