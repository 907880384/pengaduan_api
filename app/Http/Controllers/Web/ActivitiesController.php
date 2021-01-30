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
        $slug = strtolower($user->roles()->first()->slug);
        $records = Complaint::with(['typeComplaint', 'complainer', 'complaintTrackings', 'assigned']);

        if($slug == 'pegawai') {
            $records = $records->where('user_complaint_id', $user->id);
        }

        if($slug !== 'pegawai' && $slug !== 'admin') {
            $records = $records->whereHas('assigned', function($q) use($user) {
                $q->where('user_perform_id', $user->id);
            });
        }

        $records = $records->where('on_assigned', '=', true)->orderBy('updated_at', 'desc')->paginate(10); 

        return view('pages.activities.index', compact('records'));
    }
}
