<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\User;
use Helper;
use Auth;
use File;
use Storage;
use Response;

class UsersController extends Controller
{
    private function sendResponse($msg, $status=200) {
        return response(['message' => $msg], $status);
    }

    public function index()
    {
        return response(Auth::user());
    }

    public function profile(Request $req) {
        
        $pathname = null;

        $req->validate([
            "name" => "required",
            "username" => "required|unique:users,username,".Auth::user()->id, 
        ]);

        $user = User::with(['roles', 'profile'])->find(Auth::user()->id);

        $user->username = $req->username;
        $user->name = $req->name;
        $user->save();

        if($req->file('thumbnail')) {   
            $pathname = $req->file('thumbnail')->storeAs(
                'profiles', 
                'user_' . Auth::user()->id . time(). '_' . $req->file('thumbnail')->getClientOriginalName(), 
                'public'
            );
        }


        if($user->profile == null) {
            $user->profile()->create([
                'phone' => $req->phone == null ? '': $req->phone,
                'email' => $req->email == null ? '': $req->email,
                'thumbnail' => $pathname,
                'identity' => $req->identity == null ? '': $req->identity
            ]);

            return response([
                'message' => Helper::messageResponse()->PROFILE_CREATE,
            ], 200);
        }
        else {

            if($req->isChangeThumbnail == true) {
                if(file_exists(public_path($user->profile->thumbnail))) {
                    unlink(public_path($user->profile->thumbnail));
                }
            }

            //Update
            $user->profile->phone = $req->phone == null ? '': $req->phone;
            $user->profile->email = $req->email == null ? '': $req->email;
            $user->profile->thumbnail = $pathname;
            $user->profile->identity = $req->identity == null ? '': $req->identity;
            
            if($user->profile->save()) {
                return response([
                    'message' => Helper::messageResponse()->PROFILE_UPDATE,
                ], 200);
            }
        }
        
        return $this->sendResponse(Helper::messageResponse()->NOT_ACCESSED, 400);
    }

    public function getInfo() {
        $user = User::with([
            'roles',
            'profile',
            'complaints',
        ])->find(Auth::user()->id);

        if(!$user) {
            return $this->sendResponse(Helper::messageResponse()->USER_INFO_FAILED, 404);
        }

        if($user->profile != null) {
            if($user->profile->thumbnail != '' && $user->profile->thumbnail != null) {
                $path = Storage::url($user->profile->thumbnail);
                $user->profile->thumbnail = url($path);
            }
        }

        return response(['result' => $user]);
    }
}
