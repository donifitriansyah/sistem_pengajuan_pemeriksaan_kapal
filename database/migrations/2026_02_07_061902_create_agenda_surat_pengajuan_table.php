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
        Schema::create('agenda_surat_pengajuan', function (Blueprint $table) {
            $table->id();

            $table->string('nomor_surat_pengajuan')->unique();
            $table->date('tanggal_surat');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_surat_pengajuan');
    }
};
