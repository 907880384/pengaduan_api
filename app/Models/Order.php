<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'product_id',
        'qty',
        'user_id',
        'status',
        'order_date',
        'complaint_id',
    ];

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id', 'id');
    }

    public function complaint()
    {
        return $this->belongsTo(\App\Models\Complaint::class, 'complaint_id', 'id');
    }
}
