<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mode;

class ModeSeeder extends Seeder
{
    public function run(): void
    {
        // Masukkan data awal untuk Mode
        Mode::updateOrCreate(
            ['id' => 1],
            ['status' => 'presensi']
        );
    }
}