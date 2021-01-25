<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assigned extends Model
{
    protected $fillable = [
        'complaint_id',
        'user_perform_id',
        'is_working',
        'image_work',
        'description',
        'start_work',
        'end_work',
        'status_id'
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_perform_id', 'id');
    }

    public function complaint()
    {
        return $this->belongsTo(\App\Models\Complaint::class, 'complaint_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo(\App\Models\StatusProcess::class, 'status_id', 'id');
    }
}
