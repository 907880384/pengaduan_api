<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\User;
use Helper;
use Auth;
use File;

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
        
        if(Auth::user()) {

            $pathname = null;

            if($req->file('thumbnail')) {   
                $pathname = $req->file('thumbnail')->storeAs(
                    'profiles', 
                    'user_' . Auth::user()->id . time(). '_' . $req->file('thumbnail')->getClientOriginalName(), 
                    'public'
                );
            }


            $profile = Profile::find(Auth::user()->id);

            if(!$profile) {
                //Create
                $profile = Profile::create([
                    'user_id' => Auth::user()->id,
                    'phone' => $req->phone,
                    'email' => $req->email,
                    'thumbnail' => $pathname,
                    'identity' => $req->identity
                ]);

                if($profile) {
                    return $this->sendResponse(Helper::messageResponse()->PROFILE_CREATE);
                }
            }
            else {
                //Update
                $profile->phone = $req->phone;
                $profile->email = $req->email;
                $profile->thumbnail = $pathname;
                $profile->identity = $req->identity;
                
                if($profile->save()) {
                    return $this->sendResponse(Helper::messageResponse()->PROFILE_UPDATE);
                }
            }

            return $this->sendResponse(Helper::messageResponse()->PROFILE_FAILED, 400);
        }
        
        return $this->sendResponse(Helper::messageResponse()->NOT_ACCESSED, 400);
    }

    public function getInfo() {
        $user = User::with([
            'profile',
            'complaints',
        ])->find(Auth::user()->id);

        if(!$user) {
            return $this->sendResponse(Helper::messageResponse()->USER_INFO_FAILED, 404);
        }

        if($user->profile != null) {
            if($user->profile->thumbnail != '' && $user->profile->thumbnail != null) {
                $user->profile->thumbnail = url($user->profile->thumbnail);
            }
        }

        return response(['user' => $user]);
    }
}
