<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'title',
        'messages',
        'is_urgent',
        'is_finished',
        'is_assigned',
        'sender_id',
        'type_id',
        'finished_at'
    ];

    public function logs()
    {
        return $this->hasMany(\App\Models\ComplaintLog::class, 'complaint_id', 'id');
    }

    public function sender()
    {
        return $this->belongsTo(\App\User::class, 'sender_id', 'id');
    }
    
    public function assigned()
    {
        return $this->hasOne(\App\Models\Assigned::class, 'complaint_id', 'id');
    }

    public function types()
    {
        return $this->belongsTo(\App\Models\Role::class, 'type_id', 'id');
    }
}
