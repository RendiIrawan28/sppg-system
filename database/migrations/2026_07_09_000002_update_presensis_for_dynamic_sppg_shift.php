<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kolom angka disimpan sebagai menit.
        DB::statement('ALTER TABLE presensis MODIFY total_jam INT UNSIGNED NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE presensis MODIFY telat INT UNSIGNED NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE presensis MODIFY lembur INT UNSIGNED NOT NULL DEFAULT 0');

        Schema::table('presensis', function (Blueprint $table) {
            if (! Schema::hasColumn('presensis', 'status')) {
                $table->string('status', 30)->default('open')->after('lembur');
            }

            if (! Schema::hasColumn('presensis', 'checkout_type')) {
                $table->string('checkout_type', 30)->nullable()->after('status');
            }

            if (! Schema::hasColumn('presensis', 'catatan')) {
                $table->text('catatan')->nullable()->after('checkout_type');
            }
        });

        DB::table('presensis')
            ->whereNull('jam_keluar')
            ->update([
                'status' => 'open',
                'checkout_type' => null,
            ]);

        DB::table('presensis')
            ->whereNotNull('jam_keluar')
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', 'open');
            })
            ->update([
                'status' => 'closed',
                'checkout_type' => 'manual',
            ]);
    }

    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            if (Schema::hasColumn('presensis', 'catatan')) {
                $table->dropColumn('catatan');
            }

            if (Schema::hasColumn('presensis', 'checkout_type')) {
                $table->dropColumn('checkout_type');
            }

            if (Schema::hasColumn('presensis', 'status')) {
                $table->dropColumn('status');
            }
        });

        DB::statement('ALTER TABLE presensis MODIFY total_jam INT UNSIGNED NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE presensis MODIFY telat TINYINT(1) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE presensis MODIFY lembur TINYINT(1) NOT NULL DEFAULT 0');
    }
};
