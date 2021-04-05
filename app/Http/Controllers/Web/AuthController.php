<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Validator;
use Response;

class AuthController extends Controller
{
    public function login()
    {
        return view('pages.auth.login');
    } 

    public function authLogin(Request $req)
    {
        $req->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        if (Auth::attempt($req->only('username', 'password'))) {
            return redirect()->intended('dashboard');
        }

        return redirect('login')->with('error', 'Oppes! You have entered invalid credentials');
    }

    public function logout() {
        Session::flush();
        Auth::logout();
        return redirect('login');
    }

    public function downloadAndroidAPK() {
        $file = public_path()."/downloads/app-debug.apk";
        $headers = [
            'Content-Type'=>'application/vnd.android.package-archive',
            'Content-Disposition'=> 'attachment; filename="android.apk"',
        ];
        return Response::download($file, 'app-debug.apk',$headers);
    }
}
