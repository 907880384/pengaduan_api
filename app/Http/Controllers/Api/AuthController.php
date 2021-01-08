<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Auth;

class AuthController extends Controller
{
    public $secreter = 'SECRET_KEY_@SECURE';

    public function login(Request $req) {
        $validate = Validator::make($req->all(), [
            'username' => 'required|string|max:50',
            'password' => 'required|string|min:6'
        ]);

        if($validate->fails()) {
            return response(['errors' => $validate->errors()->all], 422);
        }

        if(Auth::attempt(['email' => $req->email, 'password' => $req->password])) {
            $token = Auth::user()->createToken($this->secreter)->accessToken;
            return response(['token' => $token] ,200);
        }
        else {
            return response(['message' => 'User does not exist'], 400);
        }
    }

    public function register(Request $req) {
        $validate = Validator::make($req->all(), [
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6'
        ]);

        if($validate->fails()) {
            return response(['errors' => $validate->errors()->all], 422);
        }


    }

    public function logout() {
        
    }
}
