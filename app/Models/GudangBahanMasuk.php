<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GudangBahanMasuk extends Model
{
    protected $table = 'gudang_bahan_masuks';

    protected $fillable = [
        'tanggal',
        'nama_petugas',
        'nama_supplier',
        'nama_bahan',
        'jumlah',
        'satuan',
        'kondisi_bahan',
        'tanggal_kedaluwarsa',
        'catatan',
        'foto_url',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'tanggal_kedaluwarsa' => 'date',
            'jumlah' => 'decimal:2',
        ];
    }
}