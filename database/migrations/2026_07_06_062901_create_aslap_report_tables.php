<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aslap_planning_reports', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->index();
            $table->integer('nomor')->nullable();
            $table->string('nama_penerima')->nullable();
            $table->integer('porsi_besar')->default(0);
            $table->integer('porsi_kecil')->default(0);
            $table->integer('total')->default(0);
            $table->timestamps();

            $table->index(['tanggal', 'nama_penerima']);
        });

        Schema::create('aslap_distribusi_reports', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->index();
            $table->integer('nomor')->nullable();
            $table->string('nama_penerima')->nullable();
            $table->integer('tendik')->default(0);
            $table->integer('porsi_besar')->default(0);
            $table->integer('porsi_kecil')->default(0);
            $table->integer('total')->default(0);
            $table->timestamps();

            $table->index(['tanggal', 'nama_penerima']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aslap_distribusi_reports');
        Schema::dropIfExists('aslap_planning_reports');
    }
};