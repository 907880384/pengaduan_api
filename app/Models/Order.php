<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'product_id',
        'complaint_id',
        'user_id',
        'quantity',
        'order_date',
        'is_agree',
        'agree_date',
        'user_agree_id',
        'reasons',
        'is_disagree'
    ];

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id', 'id');
    }

    public function complaint()
    {
        return $this->belongsTo(\App\Models\Complaint::class, 'complaint_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id', 'id');
    }

    public function agreeter()
    {
        return $this->belongsTo(\App\User::class, 'user_agree_id', 'id');
    }

   
}
