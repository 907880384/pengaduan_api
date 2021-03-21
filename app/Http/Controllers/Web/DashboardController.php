<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\Visitor;
use App\Models\Role;
use Auth;

class DashboardController extends Controller
{
    public function complaintStatistic($dates) {

        $user = Auth::user();

        $results = [
            "new_complaint" => $this->queryComplaint($user)->where('is_finished', false)->whereDate('created_at', '=', $dates)->count(),
            "finish_complaint" => $this->queryComplaint($user)->where('is_finished', true)->whereDate('created_at', '=', $dates)->count(),
            "total_complaint" => $this->queryComplaint($user)->whereDate('created_at', '=', $dates)->count(),

            "visitor_come" => Visitor::where('selesai', false)->where('is_deleted', false)->whereDate('created_at', '=', $dates)->count(),
            "visitor_exit" => Visitor::where('selesai', true)->where('is_deleted', false)->whereDate('created_at', '=', $dates)->count(),
            "total_visitor" => Visitor::whereDate('created_at', '=', $dates)->where('is_deleted', false)->count(),
        ];


        return $results;
    }   


    public function index() 
    {
        $dates = request()->has('dates') ? \Carbon\Carbon::parse(request()->get('dates'))->format('Y-m-d') : \Carbon\Carbon::today()->format('Y-m-d');

        $results = $this->complaintStatistic($dates);

        if(request()->ajax()) {
            return view('pages.dashboard.badge_statistic', compact('results'));
        }
        
        return view('pages.dashboard.index', compact('results'));
    }

    public function getStatistics() {
        $results = $this->complaintStatistic();
        return response()->json(['result' => $results]);
    }

    private function queryComplaint($user) {
        $slug = strtolower($user->roles()->first()->slug);
        
        if($slug != 'admin' && $slug != 'customer') {
            return Complaint::where('type_id', $user->roles()->first()->id);
        }
        
        if($slug == 'customer') {
            return Complaint::where('sender_id', $user->id);    
        }

        return Complaint::with(['sender']);
    }
    
}
