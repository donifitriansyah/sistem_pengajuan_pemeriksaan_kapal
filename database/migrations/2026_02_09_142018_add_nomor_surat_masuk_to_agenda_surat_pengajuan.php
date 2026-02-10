<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNomorSuratMasukToAgendaSuratPengajuan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('agenda_surat_pengajuan', function (Blueprint $table) {
            // Adding the 'nomor_surat_masuk' column
            $table->string('nomor_surat_masuk')->nullable(); // You can change 'nullable' to 'required' if necessary
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('agenda_surat_pengajuan', function (Blueprint $table) {
            // Dropping the 'nomor_surat_masuk' column
            $table->dropColumn('nomor_surat_masuk');
        });
    }
}
