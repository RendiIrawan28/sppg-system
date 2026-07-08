<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensis';

    protected $fillable = [
        'pegawai_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'total_jam',
        'lembur',
        'telat',
        'status',
        'checkout_type',
        'catatan',
    ];

    protected $appends = [
        'status_label',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jam_masuk' => 'datetime',
            'jam_keluar' => 'datetime',
            'total_jam' => 'integer',
            'telat' => 'integer',
            'lembur' => 'integer',
        ];
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open' => 'Sedang Bekerja',
            'closed' => 'Selesai',
            'auto_checkout' => 'Auto Check-Out',
            default => $this->jam_keluar ? 'Selesai' : 'Sedang Bekerja',
        };
    }
}
