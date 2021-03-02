<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;

class OrderController extends Controller
{
    private $page = 10;
    
    public function index()
    {
        $records = Order::with(['product', 'user']);
        $records = $records->orderBy('updated_at', 'desc')->paginate($this->page);

        $records->getCollection()->transform(function($query) {
            $query->fileImages = \App\Models\ProductFile::where('product_id', $query->product->id)->get();
            return $query;
        });    

        if(request()->ajax()) {
            return view('pages.orders.datatable', compact('records'));
        }

        return view('pages.orders.index', compact('records'));
    }

    
    public function show($id)
    {
    }

    
    
}
