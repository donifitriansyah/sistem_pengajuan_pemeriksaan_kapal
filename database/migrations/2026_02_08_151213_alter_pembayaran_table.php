<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {

            // Relasi ke penagihan
            $table->foreignId('penagihan_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('penagihan')
                  ->nullOnDelete();

            // Rename kolom agar konsisten
            if (Schema::hasColumn('pembayaran', 'bukti_pembayaran')) {
                $table->renameColumn('bukti_pembayaran', 'file');
            }

            // Tambahan data penting
            $table->date('tanggal_bayar')
                  ->nullable()
                  ->after('file');
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {

            if (Schema::hasColumn('pembayaran', 'file')) {
                $table->renameColumn('file', 'bukti_pembayaran');
            }

            $table->dropForeign(['penagihan_id']);
            $table->dropColumn([
                'penagihan_id',
                'tanggal_bayar',
                'jumlah_bayar'
            ]);
        });
    }
};
