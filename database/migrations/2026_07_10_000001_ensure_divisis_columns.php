<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('divisis')) {
            Schema::create('divisis', function (Blueprint $table) {
                $table->id();
                $table->string('nama')->unique();
                $table->time('jam_masuk')->nullable();
                $table->time('jam_keluar')->nullable();
                $table->timestamps();
            });

            return;
        }

        Schema::table('divisis', function (Blueprint $table) {
            if (! Schema::hasColumn('divisis', 'jam_masuk')) {
                $table->time('jam_masuk')->nullable()->after('nama');
            }

            if (! Schema::hasColumn('divisis', 'jam_keluar')) {
                $table->time('jam_keluar')->nullable()->after('jam_masuk');
            }
        });

        // Pastikan kolom jam tetap nullable untuk alur SPPG yang dinamis.
        DB::statement('ALTER TABLE divisis MODIFY jam_masuk TIME NULL');
        DB::statement('ALTER TABLE divisis MODIFY jam_keluar TIME NULL');
    }

    public function down(): void
    {
        // Tidak menghapus kolom agar tidak merusak data divisi yang sudah berjalan.
    }
};
