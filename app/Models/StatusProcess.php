<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Complaint;

class StatusProcess extends Model
{
    protected $fillable = ['name', 'slug'];

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'status_process_id', 'id');
    }
}
