<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribusi_reports', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->index();
            $table->string('lokasi_tujuan')->nullable();
            $table->integer('porsi_besar')->default(0);
            $table->integer('porsi_kecil')->default(0);
            $table->integer('jumlah_porsi')->default(0);
            $table->string('jam_berangkat', 20)->nullable();
            $table->string('jam_tiba', 20)->nullable();
            $table->string('status')->nullable();
            $table->string('nama_petugas')->nullable();
            $table->text('catatan')->nullable();
            $table->text('foto_url')->nullable();
            $table->timestamps();

            $table->index(['tanggal', 'status']);
        });

        Schema::create('pengolahan_suhu_reports', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->index();
            $table->string('waktu', 20)->nullable();
            $table->string('nama_produk')->nullable();
            $table->decimal('suhu_produk', 8, 2)->nullable();
            $table->string('paraf')->nullable();
            $table->string('nama_petugas')->nullable();
            $table->text('foto_url')->nullable();
            $table->timestamps();
        });

        Schema::create('pengolahan_produksi_reports', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->index();
            $table->string('menu')->nullable();
            $table->string('bahan_baku')->nullable();
            $table->decimal('qty', 12, 2)->nullable();
            $table->string('satuan_bahan')->nullable();
            $table->string('waktu_produksi', 50)->nullable();
            $table->string('jam_mulai', 20)->nullable();
            $table->decimal('hasil_akhir', 12, 2)->nullable();
            $table->string('satuan_hasil')->nullable();
            $table->string('nama_petugas')->nullable();
            $table->text('foto_url')->nullable();
            $table->timestamps();
        });

        Schema::create('persiapan_bahan_reports', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->index();
            $table->string('nama_bahan')->nullable();
            $table->decimal('banyaknya', 12, 2)->nullable();
            $table->string('satuan')->nullable();
            $table->integer('baik')->default(0);
            $table->integer('sedang')->default(0);
            $table->integer('rusak')->default(0);
            $table->string('nama_petugas')->nullable();
            $table->text('foto_url')->nullable();
            $table->timestamps();
        });

        Schema::create('persiapan_limbah_reports', function (Blueprint $table) {
            $table->id();
            $table->string('no_ba')->nullable()->index();
            $table->date('tanggal')->index();
            $table->string('nama_pihak_kedua')->nullable();
            $table->string('jenis_limbah')->nullable();
            $table->decimal('berat_limbah_kg', 12, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->string('nama_petugas')->nullable();
            $table->text('foto_url')->nullable();
            $table->timestamps();
        });

        Schema::create('pemorsian_ompreng_reports', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->index();
            $table->string('rute')->nullable();
            $table->integer('qty_ompreng_kecil')->default(0);
            $table->integer('qty_ompreng_besar')->default(0);
            $table->string('waktu_pemorsian', 20)->nullable();
            $table->string('nama_petugas')->nullable();
            $table->text('foto_url')->nullable();
            $table->timestamps();
        });

        Schema::create('pemorsian_sisa_reports', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->index();
            $table->string('rute')->nullable();
            $table->string('waktu_cek_sisa', 20)->nullable();
            $table->string('jenis_makanan')->nullable();
            $table->decimal('berat_sisa_kg', 12, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('nama_petugas')->nullable();
            $table->text('foto_url')->nullable();
            $table->timestamps();
        });

        Schema::create('pencucian_limbah_reports', function (Blueprint $table) {
            $table->id();
            $table->string('no_ba')->nullable()->index();
            $table->date('tanggal')->index();
            $table->string('nama_pihak_kedua')->nullable();
            $table->string('jenis_limbah')->nullable();
            $table->decimal('berat_limbah_kg', 12, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->string('nama_petugas')->nullable();
            $table->text('foto_url')->nullable();
            $table->timestamps();
        });

        Schema::create('kebersihan_limbah_reports', function (Blueprint $table) {
            $table->id();
            $table->string('no_ba')->nullable()->index();
            $table->date('tanggal')->index();
            $table->string('nama_pihak_kedua')->nullable();
            $table->string('jenis_limbah')->nullable();
            $table->decimal('berat_limbah_kg', 12, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->string('nama_petugas')->nullable();
            $table->text('foto_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kebersihan_limbah_reports');
        Schema::dropIfExists('pencucian_limbah_reports');
        Schema::dropIfExists('pemorsian_sisa_reports');
        Schema::dropIfExists('pemorsian_ompreng_reports');
        Schema::dropIfExists('persiapan_limbah_reports');
        Schema::dropIfExists('persiapan_bahan_reports');
        Schema::dropIfExists('pengolahan_produksi_reports');
        Schema::dropIfExists('pengolahan_suhu_reports');
        Schema::dropIfExists('distribusi_reports');
    }
};