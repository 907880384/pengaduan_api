<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeIdentity extends Model
{
    protected $fillable = [
        'name'
    ];

    public function visitors()
    {
        return $this->hasMany(\App\Models\Visitor::class, '', 'tipe_identitas', 'id');
    }
}
