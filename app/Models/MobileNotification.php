<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class MobileNotification extends Model
{
    protected $fillable = [
        'type',
        'receiver_id',
        'messages',
        'data',
        'read_at'
    ];

    public function receiver()
    {
        return $this->belongsTo(\App\User, 'receiver_id', 'id');
    }
}
