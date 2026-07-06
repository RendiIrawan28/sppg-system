<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MobileUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('mobile_users')->truncate();

        DB::table('mobile_users')->insert([
            [
                'id' => 1,
                'username' => 'aslap',
                'password' => Hash::make('mulyadiganteng'),
                'nama' => 'Mulyadi',
                'role' => 'Asisten Lapangan',
                'status' => 'Aktif',
                'divisi' => 'Asisten Lapangan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'username' => 'rendi',
                'password' => Hash::make('rendi'),
                'nama' => 'Rendi',
                'role' => 'Admin',
                'status' => 'Aktif',
                'divisi' => 'Distribusi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'username' => 'masna',
                'password' => Hash::make('masnacantik'),
                'nama' => 'Masna',
                'role' => 'Ahli Gizi',
                'status' => 'Aktif',
                'divisi' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'username' => 'yanisopia',
                'password' => Hash::make('yanisopia'),
                'nama' => 'yanisopia',
                'role' => 'Relawan',
                'status' => 'Aktif',
                'divisi' => 'Pemorsian',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'username' => 'citra',
                'password' => Hash::make('citra'),
                'nama' => 'Citra Wulaningrum',
                'role' => 'Relawan',
                'status' => 'Aktif',
                'divisi' => 'Pengolahan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'username' => 'jack123',
                'password' => Hash::make('jack123'),
                'nama' => 'Soleh Joko Prihatin',
                'role' => 'Relawan',
                'status' => 'Aktif',
                'divisi' => 'Persiapan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}