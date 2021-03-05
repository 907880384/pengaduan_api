<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use \App\Http\Traits\UsesUuid;
    
    protected $fillable = [
        'product_name',
        'spesification',
        'stock',
        'satuan',
    ];

    
    public function fileImages()
    {
        return $this->hasMany(\App\Models\ProductFile::class, 'product_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class, 'product_id', 'id');
    }
}
