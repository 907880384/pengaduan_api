<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\StatusProcess;
use Helper;

class StatusProcessController extends Controller
{
    public function index() {
        $results = StatusProcess::paginate(5);
        return $results;
    }

    public function show($id) {
        $result = StatusProcess::find($id);

        if(!$result) {
            return response(['message' => Helper::defaultMessage()->FOUND_ERROR,], 404);    
        }

        return response(['result' => $result], 200); 
    }

    public function store(Request $req) {
        $validate = Validator::make($req->all(), [
            'name' => 'required|string|max:50',
        ]);

        if($validate->fails()) {
            return response(['errors' => $validate->errors()->all()], 422);
        }

        $result = StatusProcess::create(['name' => $req->name, 'slug' => Str::slug($req->name)]);

        if(!$result) {
            return response(['message' => Helper::defaultMessage('Status Process')->CREATE_FAILED], 400);
        }

        return response(['message' => Helper::defaultMessage('Status Process')->CREATE_SUCCESS], 200);
    }

    public function update(Request $req, $id) {
        
        $validate = Validator::make($req->all(), [
            'name' => 'required|string|max:50|unique:operational_types,name,'.$id,
        ]);

        if($validate->fails()) {
            return response(['errors' => $validate->errors()->all()], 422);
        }

        $result = StatusProcess::find($id);
        $result->name = $req->name;
        $result->slug = Str::slug($req->name);
        $result->save();

        return response(['message' => Helper::defaultMessage('Status Process')->UPDATE_SUCCESS], 200);
    }

    public function delete($id) {
        $result = StatusProcess::find($id);
        $name = $result->name;

        $result->delete();

        return response(["message" => Helper::defaultMessage($name)->DELETE_SUCCESS], 200);
    }
}
