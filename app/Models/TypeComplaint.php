<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OperationalType;
use App\Models\Complaint;

class TypeComplaint extends Model
{
    protected $fillable = ['title', 'role_id'];

    // public function operationalType()
    // {
    //     return $this->belongsTo(OperationalType::class, 'operational_type_id', 'id');
    // }

    public function roleType()
    {
        return $this->belongsTo(\App\Models\Role::class, 'role_id', 'id');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'complaint_type_id', 'id');
    }
}
