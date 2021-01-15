<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TypeComplaint;
use App\Models\Role;
use Helper;

class TypeComplainController extends Controller
{
    public function index() {
        $page = 20;
        $roleId = request()->query('roleId');
        $results = TypeComplaint::with(['roleType', 'complaints'])
            ->where('role_id', $roleId)    
            ->paginate($page);
        return response($results, 200);
    }

    public function findByRole($roleId) {
        $results = TypeComplaint::where('role_id', '=', $roleId)->get();
        return response($results, 200);
    }

    public function show($id) {
        $result = TypeComplaint::with(['rolesType'])->find($id);

        if(!$result) {
            return response(['message' => Helper::defaultMessage()->FOUND_ERROR,], 404);    
        }

        return response(['result' => $result], 200); 
    }

    public function store(Request $req) {
        $req->validate([
            'title' => 'required|string',
            'role_id' => 'required'
        ]);

        $result = TypeComplaint::create([
            'title' => $req->title, 
            'role_id' => $req->role_id
        ]);

        if(!$result) {
            return response(['message' => Helper::defaultMessage('Complaint Type')->CREATE_FAILED], 400);
        }

        return response(['message' => Helper::defaultMessage('Complaint Type')->CREATE_SUCCESS], 200);
    }

    public function update(Request $req, $id) {
        
        $req->validate([
            'title' => 'required|string|unique:type_complaints,title,'. $id,
            'role_id' => 'required'
        ]);
        
        $result = TypeComplaint::find($id);
        $result->title = $req->title;
        $result->role_id = $req->role_id;
        $result->save();

        return response(['message' => Helper::defaultMessage('Complaint Type')->UPDATE_SUCCESS], 200);
    }

    public function delete($id) {
        $result = TypeComplaint::find($id);
        $name = $result->title;

        $result->delete();

        return response(["message" => Helper::defaultMessage($name)->DELETE_SUCCESS], 200);
    }
}
