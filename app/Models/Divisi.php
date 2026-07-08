<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory;

    protected $table = 'divisis';

    protected $fillable = [
        'nama',
        'jam_masuk',
        'jam_keluar',
    ];

    public function pegawais()
    {
        return $this->hasMany(Pegawai::class);
    }
}
