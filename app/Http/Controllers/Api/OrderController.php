<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Auth;
use Helper;

class OrderController extends Controller
{
    private $page = 10;

    private function sendResponse($msg, $status=200)
    {
        return response(['message' => $msg], $status);
    }

    public function index()
    {
        $user = Auth::user();
        $slug = strtolower($user->roles()->first()->slug);

        $order = Order::with([
            'product',
            'complaint',
            'user',
            'agreeter'
        ]);


        $records = $records->orderBy('order_date', 'desc')->paginate($this->page);

        return response($records, 200);

    }

    public function store(Request $req) {
        $user = Auth::user();
        $slug = strtolower($user->roles()->first()->slug);

        $req->validate([
            'complaintId' => 'required',
            'productId' => 'required',
            'quantity' => 'required'
        ]);

        if($slug == 'customer' || $slug == 'admin' ) {
            return $this->sendResponse(Helper::messageResponse()->NOT_ACCESSED, 400);
        }

        $order = Order::create([
            'product_id' => $req->productId,
            'complaint_id' => $req->complaintId,
            'user_id' => $user->id,
            'quantity' => $req->quantity,
            'order_date' => \Carbon\Carbon::now()->toDateTimeString(),
        ]);

        if(!$order) {
            return $this->sendResponse(Helper::messageResponse()->ADD_CART_FAILED, 400);
        }

        return $this->sendResponse(Helper::messageResponse()->ADD_CART_SUCCESS, 200);

    }
}
