<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;
    protected $table = 'presensis';
    protected $fillable = [
        // WAJIB DITAMBAHKAN AGAR BISA MASS ASSIGNMENT
        'pegawai_id', 
        'tanggal', 
        'jam_masuk', 
        'jam_keluar', 
        'total_jam', 
        'lembur', 
        'telat'
    ];

    public function pegawai() 
    {
        return $this->belongsTo(Pegawai::class);
    }
}
