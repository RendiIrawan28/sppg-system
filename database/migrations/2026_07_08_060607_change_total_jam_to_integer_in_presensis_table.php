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
        Schema::table('presensis', function (Blueprint $table) {
            // Ubah total_jam menjadi integer (menyimpan total menit kerja)
            $table->integer('total_jam')->default(0)->change(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            // Kembalikan ke format sebelumnya (untuk rollback)
            $table->decimal('total_jam', 4, 2)->default(0.00)->change();
        });
    }
};