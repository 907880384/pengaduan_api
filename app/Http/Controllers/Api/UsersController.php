<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class UsersController extends Controller
{
    public function index()
    {
        return response(Auth::user());
    }
}
