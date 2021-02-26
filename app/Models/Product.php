<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use \App\Http\Traits\UsesUuid;
    
    protected $fillable = [
        'product_name',
        'spesification',
        'stock_awal',
        'stock_akhir',
        'satuan',
    ];

    
    public function fileImages()
    {
        return $this->hasMany(\App\Models\ProductFile::class, 'product_id', 'id');
    }
}
