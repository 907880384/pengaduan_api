<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'complaint_type_id',
        'messages',
        'urgent',
        'finished',
        'user_complaint_id',
        'title',
        'on_assigned'
    ];


    public function typeComplaint()
    {
        return $this->belongsTo(\App\Models\TypeComplaint::class, 'complaint_type_id', 'id');
    }

    public function complainer()
    {
        return $this->belongsTo(\App\User::class, 'user_complaint_id', 'id');
    }

    public function complaintTrackings()
    {
        return $this->belongsToMany(\App\Models\Tracking::class, 'complaint_logs', 'complaint_id', 'tracking_id')->withTimestamps();
    }
    
    public function assigned()
    {
        return $this->hasOne(\App\Models\Assigned, 'complaint_id', 'id');
    }
}
