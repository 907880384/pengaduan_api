<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\Role;
use Auth;

class DashboardController extends Controller
{
    public function complaintStatistic() {
        $roles = Role::where('slug','!=', 'admin')->where('slug', '!=', 'pegawai')->get();
        $results = [];

        foreach ($roles as $role) {
            $data = [
                'name' => $role->name,
                'new' => $this->queryComplaint($role->id)->where('is_assigned', false)->count(),
                'wait' =>$this->queryComplaint($role->id)->where('is_assigned', true)->where('is_finished', false)->count(),
                'finish' => $this->queryComplaint($role->id)->where('is_finished', true)->count() ,
                'total' => $this->queryComplaint($role->id)->count(),
            ];

            $results[] = $data;
        }

        return $results;
    }   


    public function index() 
    {
        $results = $this->complaintStatistic();

        if(request()->ajax()) {
            return view('pages.dashboard.badge_statistic', compact('results'));
        }
        
        return view('pages.dashboard.index', compact('results'));
    }

    public function getStatistics() {
        $results = $this->complaintStatistic();
        return response()->json(['result' => $results]);
    }

    public function queryComplaint($roleId) {
        $complaint = Complaint::where('type_id', $roleId);

        if(Auth::user()->roles()->first()->slug == 'pegawai') {
            $complaint->where('sender_id', Auth::user()->id);
        }

        return $complaint;
    }


    
}
