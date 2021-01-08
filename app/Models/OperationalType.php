<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TypeComplaint;

class OperationalType extends Model
{
    protected $fillable = ['name', 'slug'];

    public function typeComplaints()
    {
        return $this->hasMany(TypeComplaint::class, 'operational_type_id', 'id');
    }
}
