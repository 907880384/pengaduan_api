<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\OperationalType;

class OperationalTypeController extends Controller
{
    public function show($id) {
        $result = OperationalType::find($id);

        if(!$result) {
            return response(['message' => 'Oppss. your search not found',], 404);    
        }

        return response(['result' => $result], 200); 
    }

    public function store(Request $req) {
        $validate = Validator::make($req->all(), [
            'name' => 'required|string|max:50',
        ]);

        if($validate->fails()) {
            return response(['errors' => $validate->errors()->all], 422);
        }

        $result = OperationalType::create(['name' => $req->name, 'slug' => Str::slug($req->name)]);

        if(!$result) {
            return response(['message' => 'Operational type failed created'], 400);
        }

        return response(['message' => 'Operational type create successfully'], 200);
    }

    public function update(Request $req, $id) {
        $validate = Validator::make($req->all(), [
            'name' => 'required|string|max:50|unique:operational_types,name,except,'.$id,
        ]);

        if($validate->fails()) {
            return response(['errors' => $validate->errors()->all], 422);
        }

        $result = OperationalType::find($id);
        $result->name = $req->name;
        $result->slug = Str::slug($req->name);
        $result->save();

        return response(['message' => 'Operational type update successfully'], 200);
    }

    public function delete($id) {
        $result = OperationalType::find($id);
        $name = $result->name;

        $result->delete();

        return response(["message" => "$name delete successfully"], 200);
    }
}
