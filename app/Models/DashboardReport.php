<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardReport extends Model
{
    protected $fillable = [
        'category',
        'report_date',
        'payload',
        'photo_path',
        'photo_url',
        'source',
        'submitted_by',
    ];

    protected $casts = [
        'payload' => 'array',
        'report_date' => 'date:Y-m-d',
    ];
}