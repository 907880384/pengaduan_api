<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Models\Role;

class UsersController extends Controller
{
    private $page = 10;

    public function index()
    {
        $roles = Role::where('slug', '!=', 'admin')->where('slug', '!=', 'pegawai')->get();
        $records = User::with('roles');

        if(request()->query('filterRole') != null) {
            $filterRole = request()->query('filterRole');
            $records = $records->whereHas('roles', function($q) use($filterRole) {
                $q->where('id', $filterRole);
            });
        }

        if(request()->query('filterSearch') != null) {
            $filterSearch = request()->query('filterSearch');
            $records = $records->where('name' ,'like', '%'.$filterSearch. '%');
        }

        $records = $records->orderBy('id', 'desc')->paginate($this->page);

        
        if(request()->ajax()) {
            return view('pages.users.user_pagination', compact('records'));
        }

        return view('pages.users.index', compact('records', 'roles'));
    }



    public function show($id)
    {
        $user = User::with(['usersComplaint'])->get();
        if($user) {
            return response()->json(['results' => $user], 200);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    public function create()
    {
        $roles = Role::where('id', '!=', 1)->get();
        return view('pages.users.create', compact('roles'));
    }

    public function store(Request $req)
    {
        $req->validate([
            'name' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'role_id' => 'required',
        ]);

        $user = User::create([
            'name' => $req->name,
            'username' => $req->username,
            'password' => bcrypt('12345678'),
        ]);

        if(!$user) {
            return response()->json(['message' => 'Failed to create user'], 400);
        }

        $user->roles()->attach(Role::find($req->role_id));
        return response()->json(['message' => 'Create user successfully'], 200);
    }

    public function destroy($id)
    {
        $result = User::find($id);
        $name = $result->name;
        $result->delete();
        return response()->json(["message" => "Delete $name successfully"], 200);
    }

    public function getUserByRole($roleId)
    {
        $users = User::with('roles')->whereHas('roles', function($q) use($roleId) {
            $q->where('role_id', '=', $roleId); 
        })->get();
        
        if($users) {
            return response()->json(['users' => $users], 200);
        }

        return response()->json(['message' => 'Users not found'], 404);
        
    }
}
