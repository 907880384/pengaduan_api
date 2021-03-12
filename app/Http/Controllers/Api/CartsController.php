<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Models\Product;
use Helper;
use Auth;

class CartsController extends Controller
{
    public $COOKIE_NAME = 'carts';
    public $COOKIE_TIME = 1440;

    private function sendResponse($msg, $status=200) {
        return response(['message' => $msg], $status);
    }

    

    /** SHOW CART */
    public function showCart($complaintId) {
        $user = Auth::user();
        $keys = $user->id . '_' . $complaintId;        
        
        if(strtolower($user->roles()->first()->slug) == 'pegawai') {
            return $this->sendResponse(Helper::messageResponse()->NOT_ACCESSED, 400); 
        }

        $carts = Cookie::get($this->COOKIE_NAME);

        if(!$carts) {
            return response([
                'message' => Helper::messageResponse()->SHOW_CART_EMPTY,
                'carts' => $carts,
            ], 200);
        }


        if(strtolower($user->roles()->first()->slug) == 'admin') {
            return response()->json([
                'message' => Helper::messageResponse()->SHOW_CART_SUCCCESS,
                'carts' => json_decode($carts, true)
            ], 200);
        }
        else {
            $carts = json_decode($carts, true);
            
            return response()->json([
                'message' => Helper::messageResponse()->SHOW_CART_SUCCCESS,
                'carts' => $carts[$keys]
            ], 200);

        }

        return json_decode($carts, true);
    }

    /** REMOVE CART */
    public function removeCart() {
        $cookies = Cookie::forget($this->COOKIE_NAME);

        return response(['message' => 'Keranjang belanja berhasil di hapus'], 200)->withCookie($cookies);
    }

    /** ADD CART */
    public function createCart(Request $req) {
        // Cookie::forget($this->COOKIE_NAME);

        // $user = Auth::user();
         
        // if($user->roles()->first()->slug === 'admin' || $user->roles()->first()->slug === 'pegawai') {
        //     return $this->sendResponse(Helper::messageResponse()->NOT_ACCESSED, 400);
        // }

        // $req->validate([
        //     'product_id' => 'required',
        //     'complaint_id' => 'required', 
        // ]);

        // $product = Product::with(['fileImages'])->find($req->product_id);
        // $keys = $user->id . '_' . $req->complaint_id;
        
        // if(Cookie::get($this->COOKIE_NAME)) {
        //     $carts = json_decode(Cookie::get($this->COOKIE_NAME), true);

           
        //     if($carts && array_key_exists($keys, $carts)) {
        //         $index = 0;
        //         $found = false;

        //         while($found == false && $index < count($carts[$keys])) {
        //             if($carts[$keys][$index]['id'] == $req->product_id) {
        //                 $carts[$keys][$index]['quantity'] += 1;
        //                 $found = true;
        //             }
        //             $index++;
        //         }

        //         if($found == false) {
        //             $carts[$keys][] = [
        //                 'id' => $req->product_id,
        //                 'data' => $product,
        //                 'quantity' => $req->qty
        //             ];
        //         }
                
        //         $cookies = Cookie::make($this->COOKIE_NAME, json_encode($carts), $this->COOKIE_TIME);

        //         return response()->json([
        //             'message' => Helper::messageResponse()->ADD_CART_SUCCESS
        //         ], 200)->cookie($cookies);
        //     }
            
        // }

        // $carts = [
        //     $keys => []
        // ];

        // $carts[$keys][] = [
        //     'id' => $req->product_id,
        //     'data' => $product,
        //     'quantity' => $req->qty
        // ];

        // $cookies = Cookie::make($this->COOKIE_NAME, json_encode($carts), $this->COOKIE_TIME);

        // return response()->json([
        //     'message' => Helper::messageResponse()->ADD_CART_SUCCESS
        // ], 200)->cookie($cookies);

    }

    /** UPDATE CART */
    public function updateCart(Request $req) {
        

    }
    
}
