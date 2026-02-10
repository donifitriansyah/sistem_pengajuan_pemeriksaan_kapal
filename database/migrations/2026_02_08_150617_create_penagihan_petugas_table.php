<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penagihan_petugas', function (Blueprint $table) {

            $table->id();

            $table->foreignId('penagihan_id')
                  ->constrained('penagihan')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->timestamps();

            // Cegah petugas dobel di 1 penagihan
            $table->unique(['penagihan_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penagihan_petugas');
    }
};
