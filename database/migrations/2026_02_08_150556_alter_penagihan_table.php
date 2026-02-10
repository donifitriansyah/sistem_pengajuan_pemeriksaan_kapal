<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penagihan', function (Blueprint $table) {

            // Hapus relasi user lama
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            // Tambah relasi ke pengajuan
            $table->foreignId('pengajuan_id')
                  ->after('id')
                  ->constrained('pengajuan_pemeriksaan_kapal')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('penagihan', function (Blueprint $table) {

            // Balikin user_id
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Hapus pengajuan_id
            $table->dropForeign(['pengajuan_id']);
            $table->dropColumn('pengajuan_id');
        });
    }
};
