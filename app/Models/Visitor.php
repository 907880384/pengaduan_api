<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $fillable = [
        'nama',
        'no_identitas',
        'tipe_identitas',
        'no_hp',
        'tujuan',
        'keterangan',
        'selesai',
        'time_masuk',
        'time_keluar',
        'user_id',
        'is_deleted',
    ];

    
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id', 'id');
    }

    public function fileVisitors()
    {
        return $this->hasMany(\App\Models\FileVisitor::class, 'visitor_id', 'id');
    }

    public function typeIdentity()
    {
        return $this->belongsTo(\App\Models\TypeIdentity::class, 'tipe_identitas', 'id');
    }
}
