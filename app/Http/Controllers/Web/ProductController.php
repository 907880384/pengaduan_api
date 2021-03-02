<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductFile;
use Helper;
use Storage;

class ProductController extends Controller
{
    private $page = 10;

    private function sendResponse($msg, $status=200) {
        return response()->json(['message' => $msg], $status);
    }

    public function index()
    {
        $records = Product::with(['fileImages']);

        $searchName = request()->query('name');
        $searchSpesification = request()->query('spesification');


        if($searchName && $searchName != '') {
            $records = $records->where('product_name', 'like', '%'.$searchName.'%');
        }

        if($searchSpesification && $searchSpesification != '') {
            $records = $records->where('spesification', 'like', '%'.$searchSpesification.'%');
        }


        $records = $records->orderBy('updated_at', 'desc')->paginate($this->page);

        if(request()->ajax()) {
            return view('pages.products.datatable', compact('records'));
        }

        return view('pages.products.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        $req->validate([
            'product_name' => 'required|unique:products,product_name',
            'fileImages.*' => 'mimes:png,jpg,jpeg'
        ]);


        $product = Product::create([
            'product_name' => $req->product_name,
            'spesification' => $req->spesification,
            'stock_awal' => $req->stock,
            'satuan' => $req->satuan,
        ]);

        if(!$product) {
            return $this->sendResponse(Helper::messageResponse()->PRODUCT_CREATE_FAILED, 400); 
        }

        if($req->hasFile('fileImages')) {
            $dataFile = [];

            foreach($req->file('fileImages') as $key => $file)
            {
                $filename = $product->id . '_'. time(). '_'. $key . '.' . $file->getClientOriginalName();

                $dataFile[] = [
                    'product_id' => $product->id,
                    'filename' => $filename,
                    'filepath' => $file->storeAs('products', $filename, 'public'),
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ];
            }

            if(count($dataFile) > 0) {
                ProductFile::insert($dataFile);
            }
        }

        return $this->sendResponse(Helper::messageResponse()->PRODUCT_CREATE_SUCCESS, 200); 

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::with(['fileImages'])->find($id);
        return view('pages.products.update', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $id)
    {
        $oldImages = [];

        $req->validate([
            'product_name' => 'required|unique:products,product_name,' . $id,
            'fileImages.*' => 'mimes:png,jpg,jpeg'
        ]);

        $product = Product::with(['fileImages'])->find($id);

        if(count($product->fileImages) > 0) {
            $oldImages = $product->fileImages;
        }

        $product->product_name = $req->product_name;
        $product->spesification = $req->spesification;
        $product->stock_awal = $req->stock;
        $product->satuan = $req->satuan;
        $product->save();

        if(!$product) {
            return $this->sendResponse(Helper::messageResponse()->PRODUCT_UPDATE_FAILED, 400); 
        }


        if ($req->hasFile('fileImages')) {

            if(count($oldImages) > 0) {
                $ids = [];
                foreach ($oldImages as $value) {
                    if(file_exists(public_path(). '/storage/'. $value->filepath )) {
                        unlink(public_path() . '/storage/'. $value->filepath);
                    }
                    $ids[] = $value->id;
                }
                ProductFile::whereIn('id', $ids)->delete();
            }

        
            $dataFile = [];

            foreach($req->file('fileImages') as $key => $file)
            {
                $filename = $product->id . '_'. time(). '_'. $key . '.' . $file->getClientOriginalName();

                $dataFile[] = [
                    'product_id' => $product->id,
                    'filename' => $filename,
                    'filepath' => $file->storeAs('products', $filename, 'public'),
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                ];
            }

            if(count($dataFile) > 0) {
                ProductFile::insert($dataFile);
            }
        }

        return $this->sendResponse(Helper::messageResponse()->PRODUCT_UPDATE_SUCCESS, 200); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::with(['fileImages'])->find($id);

        if(!$product) {
            return $this->sendResponse(Helper::messageResponse()->PRODUCT_NOT_FOUND, 404); 
        }

        // $name = $product->product_name;
        
        if(count($product->fileImages) > 0) {
            $fileImages = $product->fileImages;

            $ids = [];
            foreach ($fileImages as $value) {
                if(file_exists(public_path(). '/storage/'. $value->filepath )) {
                    unlink(public_path() . '/storage/'. $value->filepath);
                }
                $ids[] = $value->id;
            }
            ProductFile::whereIn('id', $ids)->delete();
        }

        $product->delete();

        return $this->sendResponse(Helper::messageResponse()->PRODUCT_DELETE_SUCCESS, 200); 
    }
}
