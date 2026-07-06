<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileUser extends Model
{
    protected $fillable = [
        'username',
        'password',
        'nama',
        'role',
        'status',
        'divisi',
    ];

    protected $hidden = [
        'password',
    ];
}