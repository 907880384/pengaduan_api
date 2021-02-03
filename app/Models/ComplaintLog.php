<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintLog extends Model
{
    protected $fillable = [
        'complaint_id',
        'log',
    ];

    public function complaint()
    {
        return $this->belongsTo(\App\Models\Complaint::class, 'complaint_id', 'id');
    }
}

