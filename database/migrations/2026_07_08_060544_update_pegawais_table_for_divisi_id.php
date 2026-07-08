<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/xxxx_xx_xx_update_pegawais_table_for_divisi_id.php
// ...
public function up(): void
{
    Schema::table('pegawais', function (Blueprint $table) {
        $table->dropColumn('divisi'); // Hapus kolom 'divisi' lama (string)
        $table->foreignId('divisi_id')
              ->nullable() // Pegawai boleh tidak punya divisi
              ->after('nama') // Letakkan setelah nama
              ->constrained('divisis') // Relasi ke tabel divisis
              ->onDelete('set null'); // Jika Divisi dihapus, set Pegawai.divisi_id jadi null
    });
}

public function down(): void
{
    Schema::table('pegawais', function (Blueprint $table) {
        $table->dropConstrainedForeignId('divisi_id');
        // $table->string('divisi')->nullable()->after('nama'); // Opsional: kembalikan kolom lama jika rollback
    });
}
// ...
};
