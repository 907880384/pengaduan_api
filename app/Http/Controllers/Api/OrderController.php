<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Auth;
use Cookie;
use Helper;

class OrderController extends Controller
{
    private $page = 10;

    private function sendResponse($msg, $status=200) {
        return response(['message' => $msg], $status);
    }

    public function index() {}

    public function addCartOrder(Request $req) {
        $user = Auth::user();
        $slug = Auth::user()->roles()->first()->slug;

        if($slug === 'admin' || $slug === 'pegawai') {
            return $this->sendResponse(Helper::messageResponse()->NOT_ACCESSED, 400);
        }

        //Validasi
        $req->validate([
            'complaint_id' => 'required',
            'product_id' => 'required',
            'qty' => 'required|numeric',
        ]);


        $cartOrders = $req->cookie('cartOrders');

        // if($cartOrders && array_key_exists($req->product_id, $cartOrders)) {

        // }
        // else {

        // }
        
        return response(['cartorders' => $cartOrders]);

    }
}
