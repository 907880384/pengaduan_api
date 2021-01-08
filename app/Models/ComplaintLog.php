<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintLog extends Model
{
    protected $fillable = [
        'complaint_id',
        'tracking_id',
    ];
}
