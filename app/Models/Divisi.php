<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory;

    protected $table = 'divisis';

    // Kolom yang dapat diisi melalui mass assignment
    // app/Models/Divisi.php
// ...
protected $fillable = [
    'nama', // PASTIKAN HANYA 'nama'
    'jam_masuk',
    'jam_keluar',
    //'toleransi_telat', // Jangan lupa tambahkan ini jika belum ada
];
// ...

    /**
     * Relasi One-to-Many: Satu Divisi memiliki banyak Pegawai.
     */
    public function pegawais()
    {
        return $this->hasMany(Pegawai::class);
    }
}
