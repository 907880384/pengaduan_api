<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;

class RolesController extends Controller
{
    public function index()
    {
        $records = Role::orderBy('id', 'desc')->paginate(10);
        return view('pages.roles.index', compact('records'));
    }
}
