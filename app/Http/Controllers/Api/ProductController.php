<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Helper;

class ProductController extends Controller
{
    private $page = 4;

    private function sendResponse($msg, $status=200) {
        return response(['message' => $msg], $status);
    }
    

    public function index()
    {
        $types = request()->query('types');
        $search = request()->query('search');


        $products = Product::with(['fileImages']);

        if($types == 'name' && $search != '') {
            $products = $products->where('product_name', 'like', '%'.$search.'%');
        }

        if($types == 'spesification' && $search != '') {
            $products = $products->where('spesification', 'like', '%'.$search.'%');
        }

        $products = $products->orderBy('updated_at', 'desc')->paginate($this->page);

        $products->data = $products->getCollection()->transform(function($query) {
            if(count($query->fileImages) > 0) {
                foreach ($query->fileImages as $file) {
                    $file->filepath = url('storage/'.$file->filepath);
                }
            }
            return $query;
        });  

        return response($products, 200);

    }


    public function show($id)
    {
        $product = Product::with(['fileImages'])->find($id);
        
        if(!$product) {
            return $this->sendResponse(Helper::messageResponse()->PRODUCT_NOT_FOUND, 404);    
        }

        if(count($product->fileImages) > 0) {
            foreach ($product->fileImages as $file) {
                $file->filepath = url($file->filepath);
            }
        }

        return response(['product' => $product], 200); 

    }

}
