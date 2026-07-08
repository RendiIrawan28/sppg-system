<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mode extends Model
{
    use HasFactory;

    // Kolom 'status' harus ada di sini agar Mass Assignment diizinkan.
    protected $fillable = ['status']; 
    
    // Karena tabel 'modes' hanya berisi satu baris data mode, 
    // kita nonaktifkan kolom timestamps.
    public $timestamps = false; 

    // Opsional: Pastikan Primary Key adalah 'id'
    protected $primaryKey = 'id';
}
