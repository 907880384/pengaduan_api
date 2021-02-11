<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assigned extends Model
{
    protected $fillable = [
        'complaint_id',
        'executor_id',
        'is_accepted',
        'filepath',
        'filename',
        'description',
        'start_work',
        'end_work',
        'status_id',
        'attacher_id',
    ];

    public function executor()
    {
        return $this->belongsTo(\App\User::class, 'executor_id', 'id');
    }

    public function complaint()
    {
        return $this->belongsTo(\App\Models\Complaint::class, 'complaint_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo(\App\Models\StatusProcess::class, 'status_id', 'id');
    }

    public function attacher() {
        return $this->belongsTo(\App\User::class, 'attacher_id', 'id');
    }
}
