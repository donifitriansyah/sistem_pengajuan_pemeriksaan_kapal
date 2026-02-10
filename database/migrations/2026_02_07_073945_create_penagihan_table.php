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
        Schema::create('penagihan', function (Blueprint $table) {
            $table->id();

            // Jenis tarif
            $table->string('jenis_tarif');

            // Jumlah petugas
            $table->integer('jumlah_petugas');

            // Relasi ke users
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Waktu mulai & selesai
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');

            // Total tarif
            $table->decimal('total_tarif', 15, 2);

            // Relasi ke pembayaran (bukti pembayaran)
            $table->foreignId('pembayaran_id')
                  ->nullable()
                  ->constrained('pembayaran')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penagihan');
    }
};
