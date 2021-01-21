<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class UsersController extends Controller
{
    public function index()
    {
        $records = User::orderBy('id', 'desc')->paginate(10);
        return view('pages.users.index', compact('records'));
    }
}
