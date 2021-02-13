<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\User;
use Auth;
use Storage;

class AuthController extends Controller
{
    public $secreter = 'SECRET_KEY_@SECURE';

    public function login(Request $req) {

        $req->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);
        
        if(Auth::attempt(['username' => $req->username, 'password' => $req->password])) {
            $token = Auth::user()->createToken($this->secreter)->accessToken;
            $user = User::with(['roles', 'profile'])->find(Auth::user()->id);

            if($user->profile != null) {
                $path = Storage::url($user->profile->thumbnail);
                if($user->profile->thumbnail != '' && $user->profile->thumbnail != null) {
                    $user->profile->thumbnail = url($path); 
                }
            }

            return response(['token' => $token, 'user' => $user] ,200);
        }
        else {
            return response(['message' => 'User does not exist'], 400);
        }
    }

    public function register(Request $req) {
        $req->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6'
        ]);

        if($validate->fails()) {
            return response(['errors' => $validate->errors()->all], 422);
        }

        $user = User::create([
            'name' => $req->name,
            'username' => $req->username,
            'password' => bcrypt($req->password),
            'remember_token' => Str::random(10),
        ]);

        if($user) {
            $token = $user->createToken($this->secreter)->accessToken;
            return response(['token' => $token], 200);
        }

        return response(['message' => 'Register new account failed'], 400);

    }

    public function logout(Request $req) {
        if($req->user()->token()->revoke()) {
            return response(['message' => 'Logout success'], 200);
        }
        
        return response(['message' => 'Logout failed'], 400);
    }
}
