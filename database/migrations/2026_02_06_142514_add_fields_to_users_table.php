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
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'nama_petugas');

            $table->string('nama_perusahaan')->after('nama_petugas');
            $table->string('no_hp', 20)->after('nama_perusahaan');
            $table->string('wilayah_kerja')->after('no_hp');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('nama_petugas', 'name');

            $table->dropColumn([
                'nama_perusahaan',
                'no_hp',
                'wilayah_kerja',
            ]);
        });
    }
};
