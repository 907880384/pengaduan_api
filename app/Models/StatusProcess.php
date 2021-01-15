<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Complaint;

class StatusProcess extends Model
{
    protected $fillable = ['name', 'slug'];
}
