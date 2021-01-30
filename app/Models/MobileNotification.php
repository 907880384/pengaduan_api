<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class MobileNotification extends Model
{
    protected $fillable = [
        'type',
        'receiver_id',
        'data',
        'read_at'
    ];
}
