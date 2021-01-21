<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TypeComplaint;
use App\Models\Role;

class TypeComplaintController extends Controller
{
    public function index() {
        $records = TypeComplaint::with(['roleType'])->orderBy('updated_at', 'desc')->paginate(10);
        return view('pages.type_complaints.index', compact('records'));
    }

    public function show($id)
    {

    }

    public function create()
    {
        $roles = Role::where('id', '!=', 1)->where('id', '!=', 2)->get();
        return view('pages.type_complaints.create', compact('roles'));
    }

    public function edit($id)
    {
        $record = TypeComplaint::find($id);
        $roles = Role::where('id', '!=', 1)->where('id', '!=', 2)->get();
        return view('pages.type_complaints.update', compact('record', 'roles'));
    }

    public function store(Request $req)
    {

    }

    public function update(Request $req, $id)
    {
        $req->validate([
            'title' => 'required|string|unique:type_complaints,title,'. $id,
            'role_id' => 'required'
        ]);

        $result = TypeComplaint::find($id);
        $result->title = $req->title;
        $result->role_id = $req->role_id;
        $result->save();

        if($result) {
            return response()->json(['message' => 'Update Success'], 200);
        }

        return response()->json(['message' => 'Update Failed'], 400);
    }

    public function destroy($id)
    {
        $result = TypeComplaint::find($id);
        $name = $result->title;
        $result->delete();
        return response()->json(['message', 'Delete successfully'], 200);
    }
}
