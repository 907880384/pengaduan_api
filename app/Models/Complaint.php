<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'complaint_type_id',
        'messages',
        'status_process_id',
        'thumbnails',
        'user_accepted_id',
        'approver_id',
    ];

    public function typeComplaint()
    {
        return $this->belongsTo(\App\Models\TypeComplaint::class, 'complaint_type_id', 'id');
    }

    public function statusProcess()
    {
        return $this->belongsTo(\App\Models\StatusProcess::class, 'status_process_id', 'id');
    }

    public function approver()
    {
        return $this->belongsTo(\App\User::class, 'approver_id', 'id');
    }

    public function userAccepted()
    {
        return $this->belongsTo(\App\User::class, 'user_accepted_id', 'id');
    }

    public function complaintTrackings()
    {
        return $this->belongsToMany(\App\Models\Tracking::class, 'complaint_logs', 'complaint_id', 'tracking_id');
    }
    
}
