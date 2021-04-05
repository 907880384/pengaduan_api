<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Events\OrdersCartEvent;
use DB;
use Auth;
use DataTables;

class OrderController extends Controller
{

    public function index()
    {
        return view('pages.orders.index');
    }

    public function listOrder(Request $req) {
        $user = Auth::user();
        
        $records = Order::with([
            'product',
            'complaint',
            'user',
            'agreeter'
        ]);

        $orderDate = $req->query('orderDate');
        $orderStatus = $req->query('orderStatus');

        if($orderDate && $orderDate != null) {
            $records = $records->whereDate('order_date', \Carbon\Carbon::parse($orderDate)->format('Y-m-d'));
        }

        if($orderStatus && $orderStatus != null) {
            switch (strtolower($orderStatus)) {
                case 'wait':
                    $records = $records->where('is_agree', false)->where('is_disagree', false);
                    break;

                case 'accept': 
                    $records = $records->where('is_agree', true);
                    break;

                case 'cancel':
                    $records = $records->where('is_agree', false)->where('is_disagree', true);
                    break;
                default:
                    break;
            }
        }


        $records->orderBy('order_date', 'desc')->get();

        return Datatables::of($records)->addIndexColumn()
            ->addColumn('product_name', function($row) {
                return $row->product->product_name;
            })
            ->addColumn('status', function($row) {
                if($row->is_agree) {
                    return 'DISETUJUI';
                }
                else {
                    if($row->is_disagree) {
                        return 'DITOLAK';
                    }
                    else {
                        return 'MENUNGGU';
                    }
                }

            })
            ->addColumn('action', function($row) use($user) {
                $str = '';

                if(strtolower($user->roles()->first()->slug) == 'admin') {
                    if(!$row->is_agree && !$row->is_disagree) {
                        $str .= '<button type="button" class="btn btn-success btn-sm" onclick="setAgree('.$row->id.')"><i class="fas fa-check"></i>TERIMA</button>';

                        $str .= '&nbsp;<button type="button" class="btn btn-danger btn-sm" onclick="setDisagree('.$row->id.')"><i class="fas fa-times"></i> TOLAK
                        </button>';
                    }
                }
                return $str;
            })->rawColumns(['action'])->make(true);

    }

    public function countNewOrder() {
        $totalOrder = Order::where('is_agree', false)
        ->where('is_disagree', false)
        ->whereDate('order_date', \Carbon\Carbon::today())
        ->count();

        return response([
            'totalOrder' => $totalOrder
        ], 200);
    }

    public function disagree(Request $req) {
        $req->validate([
            'orderId' => 'required',
            'orderReason' => 'required'
        ]);

        $order = Order::find($req->orderId);
        $order->reasons = $req->orderReason;
        $order->is_disagree = true;
        if($order->save()) {

            $product = Product::find($order->product_id);
            $product->stock = $product->stock + $order->quantity;
            $product->save();

            event(new OrdersCartEvent(
                $order, 
                $order->user_id,
                "DISAGREE_ORDER"
            ));

            return response([
                'message' => 'Permintaan penolakan pesanan anda disimpan'
            ], 200);
        }

        return response([
            'message' => 'Permintaan penolakan pesanan gagal'
        ], 400);
    }

    public function agreed($id) {
        $order = Order::find($id);
        $order->is_agree = true;
        $order->agree_date = \Carbon\Carbon::now()->toDateTimeString();
        $order->user_agree_id = Auth::user()->id;
        $order->save();

        if(!$order) {
            return response(['message' => 'Persetujuan pesanan barang, tidak dapat diakses'], 400);
        }

        event(new OrdersCartEvent(
            $order, 
            $order->user_id,
            "AGREE_ORDER"
        ));

        return response([
            'message' => 'Pesanan barang disetujui'
        ], 200);
    }

    public function showListCart() {

        $product_id = request()->query('product_id');
        $filter_date = request()->query('filter_date');

        $orders = Order::with([
            'agreeter',
            'user',
            'complaint',
            'product'
        ])->where('is_agree', false)->where('is_disagree', false);

        if($product_id != 'all') {
            $orders = $orders->where('product_id', $product_id);
        }

        if($filter_date != '') {
            $orders = $orders->whereDate('order_date', \Carbon\Carbon::parse($filter_date)->format('Y-m-d'));
        }

        $orders = $orders->orderBy('created_at', 'desc')->paginate(10);
        
        $orders->getCollection()->transform(function($query) {
            $query->productFiles = \App\Models\ProductFile::where('product_id', $query->product_id)->get();
            return $query;
        });    


        $products = Order::select('product_id', 'products.product_name as product_name')
        ->join('products', 'products.id', '=', 'orders.product_id')
        ->groupBy('product_id')
        ->get();

        if(request()->ajax()) {
            return view('pages.carts.carts', compact('orders'));
        }
        return view('pages.carts.list_cart', compact('products', 'orders'));
    }
}
