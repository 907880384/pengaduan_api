<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\OrdersCartEvent;
use App\Models\Product;
use App\Models\Order;
use App\Models\ProductFile;
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

    public function show($id) {
        $order = Order::with([
            'product',
            'complaint',
            'user',
            'agreeter'
        ])->find($id);

        if(!$order) {
            return $this->sendResponse(Helper::messageResponse()->CART_NOT_FOUND, 400);
        }

        $pFiles = ProductFile::where('product_id', $order->product_id)->get();

        if(count($pFiles) > 0) {
            foreach ($pFiles as $file) {
                $file->filepath = url('storage/'. $file->filepath);
            }
        }

        $order->product_files = $pFiles;
        return response(['order' => $order], 200);  
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

    public function getOrderByParam(Request $req) {
        $user = Auth::user();
        $orders = Order::with([
            'product',
            'complaint',
            'user',
            'agreeter'
        ])
        ->where('is_agree', false)
        ->where('is_disagree', false)
        ->where('user_id', $user->id);

        if($req->has('orderDate') && $req->query('orderDate') != null && !empty($req->query('orderDate'))) {
            $orders = $orders->whereDate('order_date', \Carbon\Carbon::parse($req->query('orderDate'))->format('Y-m-d'));
        }

        $orders = $orders->orderBy('order_date', 'DESC')->get();

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

        //Cek Produk
        $product = Product::find($req->productId);

        if(!$product) {
            return $this->sendResponse(Helper::messageResponse()->PRODUCT_NOT_FOUND, 400);
        }

        //Cek Stok Produk
        if($product->stock >= $req->quantity) {
            $stock = (int) $product->stock - (int) $req->quantity;
            $product->stock = $stock;
            $product->save();
        }
        else {
            return $this->sendResponse(Helper::messageResponse()->ADD_CART_REJECTED, 400);
        }

        //Cek Orderan
        $myOrder = Order::with([
            'product',
            'complaint',
            'user',
            'agreeter'
        ])->where('product_id', $req->productId)->where('complaint_id', $req->complaintId)->first();

        //Sudah Pernah Ada
        if($myOrder) {
            $myOrder->quantity = $myOrder->quantity + (int) $req->quantity;
            $myOrder->order_date = \Carbon\Carbon::now()->toDateTimeString();
            $myOrder->save();

            if(!$myOrder) {
                return $this->sendResponse(Helper::messageResponse()->ADD_CART_FAILED, 400);
            }

            event(new OrdersCartEvent(
                $myOrder, 
                "admin", 
                "ADD_ORDER"
            ));

            return response([
                'message' => Helper::messageResponse()->ADD_CART_SUCCESS,
                'order' => $myOrder
            ], 200);

        }
        else {
            //Buat baru
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

            $findOrder = Order::with([
                'product',
                'complaint',
                'user',
                'agreeter'
            ])->find($order->id);
    
            event(new OrdersCartEvent(
                $findOrder, 
                "admin", 
                "ADD_ORDER"
            ));
    
            return response([
                'message' => Helper::messageResponse()->ADD_CART_SUCCESS,
                'order' => $findOrder
            ], 200);
        }

    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required'
        ]);

        $order = Order::find($id);
        $order->quantity = $request->quantity;
        $order->save();
        
        if($order) {
            return response(['message' => 'Pesanan anda berhasil di ubah']);
        }

        return response(['message' => 'Pesanana anda gagal diubah'], 400);
    }
}
