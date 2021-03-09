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

    private function sendResponse($msg, $status=200)
    {
        return response(['message' => $msg], $status);
    }

    public function index()
    {
        $cookies = json_decode(Cookie::get('productCart'));

        return response(['cokkies' => $cookies]);
    }
}
