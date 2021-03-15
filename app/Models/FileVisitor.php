<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileVisitor extends Model
{
    protected $fillable = [
        'visitor_id',
        'filename',
        'filepath'
    ];

    public function visitor()
    {
        return $this->belongsTo(\App\Models\Visitor::class, 'visitor_id', 'id');
    }
}