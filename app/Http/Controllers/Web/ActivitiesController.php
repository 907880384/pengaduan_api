<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;
use Auth;

class ActivitiesController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if(!$user) {
            return abort(404);
        }

        $slug = strtolower($user->roles()->first()->slug);
        
        $records = Complaint::with(['sender', 'assigned', 'logs']);


        if($slug === 'pegawai') {
            $records = $records->where('sender_id', $user->id);
        }

        if($slug != 'admin' && $slug != 'pegawai') {
            $records = $records->whereHas('assigned', function($q) use($user) {
                $q->where('executor_id', $user->id);
            });
        }

        $records = $records->where('is_assigned', '=', true)->orderBy('updated_at', 'desc')->paginate(10); 

        $records->getCollection()->transform(function($query) {
            if($query->assigned != null) {
                $query->executor = \App\User::find($query->assigned->executor_id);
            }
            return $query;
        }); 
         
        return view('pages.activities.index', compact('records'));
    }
}
