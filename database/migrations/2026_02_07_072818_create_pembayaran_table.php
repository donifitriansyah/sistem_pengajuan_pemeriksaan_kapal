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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();

            // Simpan path file bukti pembayaran
            $table->string('bukti_pembayaran')->nullable();

            // Status pembayaran
            $table->enum('status', [
                'menunggu',
                'diterima',
                'ditolak',
            ])->default('menunggu');

            // Keterangan admin / sistem
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
