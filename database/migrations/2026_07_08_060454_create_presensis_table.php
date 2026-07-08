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
        Schema::create('presensis', function (Blueprint $table) {
    $table->id();
    $table->foreignId('pegawai_id')->constrained()->onDelete('cascade');
    $table->date('tanggal');
    $table->dateTime('jam_masuk');
    $table->dateTime('jam_keluar')->nullable(); // Kritis: Harus NULLABLE
    $table->decimal('total_jam', 4, 2)->default(0);
    $table->boolean('telat')->default(false);
    $table->boolean('lembur')->default(false);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
