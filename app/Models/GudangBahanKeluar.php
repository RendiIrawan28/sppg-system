<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GudangBahanKeluar extends Model
{
    protected $table = 'gudang_bahan_keluars';

    protected $fillable = [
        'tanggal',
        'nama_petugas',
        'nama_penerima',
        'divisi_penerima',
        'nama_bahan',
        'jumlah',
        'satuan',
        'catatan',
        'foto_url',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jumlah' => 'decimal:2',
        ];
    }
}