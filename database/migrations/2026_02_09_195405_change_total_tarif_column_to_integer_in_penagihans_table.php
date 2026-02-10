<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('penagihan', function (Blueprint $table) {
        $table->integer('total_tarif')->change(); // Change column type to integer
    });
}

public function down()
{
    Schema::table('penagihan', function (Blueprint $table) {
        $table->decimal('total_tarif', 15, 2)->change(); // Revert to decimal if needed
    });
}

};
