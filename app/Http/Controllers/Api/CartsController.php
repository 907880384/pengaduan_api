<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Models\Product;
use Helper;
use Auth;
use Cart;

class CartsController extends Controller
{
    public function createCart(Request $req) {

        $user = Auth::user();

        if($user->roles()->first()->slug === 'admin' || $user->roles()->first()->slug === 'pegawai') {
            return $this->sendResponse(Helper::messageResponse()->NOT_ACCESSED, 400);
        }

        $req->validate([
            'product_id' => 'required',
            'complaint_id' => 'required', 
            'qty' => 'required',
        ]);

        $product = Product::with(['fileImages'])->find($req->product_id);
        
        $carts = [
            'complaint_id' => $req->complaint_id,
            'product_id' => $req->product_id,
            'quantity' => $req->qty,
            'associatedModel' => $product,
        ];

        Cart::session($user->id)->add($carts);

        return $this->sendResponse(Helper::messageResponse()->ADD_CART_SUCCESS, 200);

    }

    public function showCart() {
        $user = Auth::user();
        $carts = Cart::getContent();
        return response()->json(['carts' => $carts]); 

        // if(strtolower($user->roles()->first()->slug) == 'admin') {
               
        // }


    }
    
}
