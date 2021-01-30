<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use Helper;

class RolesController extends Controller
{
    public function index() {
        $page = 20;
        $results = Role::with(['permissions', 'users', 'typeComplaints'])->paginate($page);
        return response($results, 200);
    }

    public function getOnlyOperationalsRoles() {
        $results = Role::where('slug', '!=', 'admin')->where('slug', '!=', 'pegawai')->get();
        return response($results, 200);
    }

    public function show($id) {
        $result = Role::find($id);

        if(!$result) {
            return response(['message' => Helper::defaultMessage()->FOUND_ERROR,], 404);    
        }

        return response(['result' => $result], 200); 
    }

    public function store(Request $req) {
        $req->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:roles,slug'
        ]);

        $result = Role::create([
            'name' => $req->name, 
            'slug' => $req->slug
        ]);

        if(!$result) {
            return response(['message' => Helper::defaultMessage('Role')->CREATE_FAILED], 400);
        }

        return response(['message' => Helper::defaultMessage('Role')->CREATE_SUCCESS], 200);
    }

    public function update(Request $req, $id) {
        
        $req->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:roles,slug,'.$id
        ]);
        
        $result = Role::find($id);
        $result->name = $req->name;
        $result->slug = $req->slug;
        $result->save();

        return response(['message' => Helper::defaultMessage('Role')->UPDATE_SUCCESS], 200);
    }

    public function destroy($id) {
        $result = Role::find($id);
        $name = $result->name;

        $result->delete();

        return response(["message" => Helper::defaultMessage($name)->DELETE_SUCCESS], 200);
    }
}
