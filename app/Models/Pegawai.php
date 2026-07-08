<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;
    protected $table = 'pegawais';
    protected $fillable = ['nama', 'divisi_id', 'uid_kartu', 'status'];

    public function divisi() 
    {
        return $this->belongsTo(Divisi::class);
    }

    public function presensi() 
    {
        return $this->hasMany(Presensi::class);
    }
}
