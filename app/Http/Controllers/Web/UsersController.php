<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Models\Role;
use App\Imports\UserImport;
use Auth;
use DataTables;
use File;
use Maatwebsite\Excel\Facades\Excel;

class UsersController extends Controller
{
    private $page = 10;

    private function getRoles() {
        $slug = strtolower(Auth::user()->roles()->first()->slug);
        $roles = Role::where('slug', '!=', 'developer');

        if($slug == 'admin') {
            $roles = $roles->where('slug', '!=', 'admin');
        }
        $roles = $roles->get();

        $roles->transform(function($query) {
            $query->fullslug = implode(
                ' ', 
                array_map('ucfirst', explode('-', $query->slug))
            );
            return $query;
        });
        return $roles;
    }

    public function index()
    {
        $roles = $this->getRoles();
        return view('pages.users.index', compact('roles'));
    }

    public function listUsers(Request $req) {
        $user = Auth::user();
        $slug = strtolower(Auth::user()->roles()->first()->slug);
        $records = User::with('roles');

        if($slug == 'admin') {
            $records = $records->whereHas('roles', function($q) {
                $q->where('slug', '!=', 'developer');
                $q->where('slug', '!=', 'admin');
            });
        }

        if($slug == 'developer') {
            $records = $records->whereHas('roles', function($q) {
                $q->where('slug', '!=', 'developer');
            });
        }

        if($req->has('filterRole')) {
            $records = $records->whereHas('roles', function($q) use($req) {
                $q->where('id', $req->query('filterRole'));
            });
        }

        if($req->has('filterSearch') && $req->query('filterSearch') != '') {
            $records = $records->where('name' ,'like', '%'.$req->query('filterSearch'). '%');
        }

        $records->orderBy('created_at', 'desc')->get();


        return Datatables::of($records)->addIndexColumn()
            ->addColumn('role_name', function($row) {
                if($row->roles[0]->slug == 'admin') {
                    return $row->roles[0]->name;
                }
                return ucfirst($row->roles[0]->alias) . ' (' . $row->roles[0]->name. ')';
            })
            ->addColumn('action', function($row) use($user) {
                $str = '';
                if($user->roles()->first()->slug == 'admin' || $user->roles()->first()->slug == 'developer') {
                    $str .= '<button type="button" class="btn btn-danger btn-sm" onclick="deleteRow('.$row->id.')"><i class="fas fa-trash"></i> HAPUS
                    </button>';
                }
                    
                return $str;
            })->rawColumns(['action'])->make(true);

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
        $roles = $this->getRoles();  
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

    public function viewUploadUser() {
        return view('pages.users.upload_user');
    }

    public function uploadUserFile(Request $req) {
        $req->validate([
            'userfile' => 'required'
        ]);

        
        $extension = File::extension($req->file('userfile')->getClientOriginalName());
        

        if($extension == 'xlsx' || $extension == 'xls') {
            $rows = Excel::toArray(new UserImport, $req->file('userfile'));
            $errors = [];
            $success = [];

            foreach ($rows[0] as $key => $value) {
                if($key > 0) {
                    
                    if($value[0] != null && !empty($value[0])) {
                        if($this->foundUser($value[0], $value[1]) == true) {
                            $errors[] = [$value[0], $value[1], $value[2], $value[3]];
                        }
                        else {
                            $success[] = [$value[0], $value[1], $value[2], $value[3]];
                        }
                    }
                    
                }
            }

            if(count($success) > 0) {
                $this->insertUserArray($success);

                if(count($errors) > 0) {
                    return response([
                        'success' => false,
                        'message' => 'Beberapa data pengguna berhasil disimpan',
                        'errors' => $errors,
                        'success' => $success 
                    ], 200);
                }

                return response([
                    'success' => true,
                    'message' => 'Data pengguna berhasil disimpan',
                    'success' => $success
                ], 200);

            }
            else {
                return response([
                    'success' => false,
                    'message' => 'Seluruh data pengguna gagal disimpan',
                    'errors' => $errors 
                ], 200);
            }
        }


        return response([
            'message' => 'Ekstensi file yang anda masukkan tidak mendukung'
        ], 400);

    }

    public function foundUser($name, $username) {
        $user = User::where('name', $name)->orWhere('username', $username)->first();
        return $user ? true : false;
    }

    public function insertUserArray($data) {
        foreach ($data as $value) {
            $password = $value[2] != null && !empty($value[2]) ? $value[2] : '12345678';
            $user = User::create([
                'name' => $value[0],
                'username' => $value[1],
                'password' =>  bcrypt($password),
            ]);
            $user->roles()->attach(Role::where('slug',$value[3])->first());
        }
    }
}
