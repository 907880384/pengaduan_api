<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ComplaintLog;

class Tracking extends Model
{
    protected $fillable = ['description'];

    public function trackingComplaints()
    {
        return $this->belongsToMany(\App\Models\Complaint::class, 'complaint_logs', 'complaint_id', 'tracking_id');
    }
}
