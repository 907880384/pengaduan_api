<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Helper;

class ProductController extends Controller
{
    private $page = 10;

    private function sendResponse($msg, $status=200) {
        return response(['message' => $msg], $status);
    }
    

    public function index()
    {
        $products = Product::with(['fileImages']);

        $searchName = request()->query('name');
        $searchSpesification = request()->query('spesification');


        if($searchName && $searchName != '') {
            $products = $products->where('product_name', 'like', '%'.$searchName.'%');
        }

        if($searchSpesification && $searchSpesification != '') {
            $products = $products->where('spesification', 'like', '%'.$searchSpesification.'%');
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
