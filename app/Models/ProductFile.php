<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductFile extends Model
{
    protected $fillable = [
        'product_id',
        'filename',
        'filepath'
    ];

    
    public function products()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id', 'id');
    }
}
