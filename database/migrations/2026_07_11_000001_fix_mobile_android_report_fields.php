<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pengolahan_produksi_reports')) {
            DB::statement('ALTER TABLE pengolahan_produksi_reports MODIFY bahan_baku TEXT NULL');
            DB::statement('ALTER TABLE pengolahan_produksi_reports MODIFY qty TEXT NULL');
            DB::statement('ALTER TABLE pengolahan_produksi_reports MODIFY satuan_bahan TEXT NULL');
        }

        if (Schema::hasTable('persiapan_bahan_reports')) {
            Schema::table('persiapan_bahan_reports', function (Blueprint $table) {
                if (! Schema::hasColumn('persiapan_bahan_reports', 'keterangan')) {
                    $table->text('keterangan')->nullable()->after('rusak');
                }
            });
        }

        foreach (['persiapan_limbah_reports', 'pencucian_limbah_reports', 'kebersihan_limbah_reports'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (! Schema::hasColumn($tableName, 'nama_pihak_pertama')) {
                    $table->string('nama_pihak_pertama')->nullable()->after('tanggal');
                }

                if (! Schema::hasColumn($tableName, 'jabatan_pihak_pertama')) {
                    $table->string('jabatan_pihak_pertama')->nullable()->after('nama_pihak_pertama');
                }

                if (! Schema::hasColumn($tableName, 'alamat_pihak_pertama')) {
                    $table->text('alamat_pihak_pertama')->nullable()->after('jabatan_pihak_pertama');
                }

                if (! Schema::hasColumn($tableName, 'jabatan_pihak_kedua')) {
                    $table->string('jabatan_pihak_kedua')->nullable()->after('nama_pihak_kedua');
                }

                if (! Schema::hasColumn($tableName, 'alamat_pihak_kedua')) {
                    $table->text('alamat_pihak_kedua')->nullable()->after('jabatan_pihak_kedua');
                }
            });
        }
    }

    public function down(): void
    {
        // Kolom sengaja tidak dihapus saat rollback agar data operasional tidak hilang.
    }
};
