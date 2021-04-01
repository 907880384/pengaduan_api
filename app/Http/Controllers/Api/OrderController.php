<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\OrdersCartEvent;
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

        $records = Order::with([
            'product',
            'complaint',
            'user',
            'agreeter'
        ]);


        $records = $records->orderBy('order_date', 'desc')->paginate($this->page);

        return response($records, 200);

    }

    public function getOrderWait() {
        $user = Auth::user();

        $orders = Order::with([
            'product',
            'complaint',
            'user',
            'agreeter'
        ])
        ->where('is_agree', false)
        ->where('is_disagree', false)
        ->where('user_id', $user->id)
        ->whereDate('order_date', \Carbon\Carbon::today())
        ->orderBy('order_date', 'DESC')
        ->get();

        if(!$orders){
            return $this->sendResponse(Helper::messageResponse()->CART_NOT_FOUND, 400);
        }
            
        return response(['orders' => $orders], 200); 
    }

    public function store(Request $req) {
        $user = Auth::user();
        $slug = strtolower($user->roles()->first()->slug);

        $req->validate([
            'complaintId' => 'required',
            'productId' => 'required',
            'quantity' => 'required|numeric'
        ]);

        if($slug == 'customer' || $slug == 'admin' ) {
            return $this->sendResponse(Helper::messageResponse()->NOT_ACCESSED, 400);
        }

        $product = Product::find($req->productId);

        if(!$product) {
            return $this->sendResponse(Helper::messageResponse()->PRODUCT_NOT_FOUND, 400);
        }

        if($product->stock >= $req->quantity) {
            $stock = (int) $product->stock - (int) $req->quantity;
            $product->stock = $stock;
            $product->save();
        }
        else {
            return $this->sendResponse(Helper::messageResponse()->ADD_CART_REJECTED, 400);
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

        event(new OrdersCartEvent(
            $order, 
            "admin", 
            "ADD_ORDER"
        ));

        return response([
            'message' => Helper::messageResponse()->ADD_CART_SUCCESS,
            'order' => $order
        ], 200);

    }
}
