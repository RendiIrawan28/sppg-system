<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gudang_bahan_masuks', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nama_petugas')->nullable();
            $table->string('nama_supplier')->nullable();
            $table->string('nama_bahan');
            $table->decimal('jumlah', 12, 2)->default(0);
            $table->string('satuan', 50)->nullable();
            $table->string('kondisi_bahan')->nullable();
            $table->date('tanggal_kedaluwarsa')->nullable();
            $table->text('catatan')->nullable();
            $table->string('foto_url')->nullable();
            $table->timestamps();

            $table->index('tanggal');
            $table->index('nama_bahan');
        });

        Schema::create('gudang_bahan_keluars', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nama_petugas')->nullable();
            $table->string('nama_penerima')->nullable();
            $table->string('divisi_penerima')->nullable();
            $table->string('nama_bahan');
            $table->decimal('jumlah', 12, 2)->default(0);
            $table->string('satuan', 50)->nullable();
            $table->text('catatan')->nullable();
            $table->string('foto_url')->nullable();
            $table->timestamps();

            $table->index('tanggal');
            $table->index('nama_bahan');
            $table->index('divisi_penerima');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gudang_bahan_keluars');
        Schema::dropIfExists('gudang_bahan_masuks');
    }
};