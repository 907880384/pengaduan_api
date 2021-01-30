<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;
use Auth;

class InformationController extends Controller
{
    public function getComplaintInfo()
    {
        $user = Auth::user();
        $slug = $user->roles()->first()->slug;
        
        if(strtolower($slug) == 'admin') {
            return response(['info' => [
                'totalComplaint' => Complaint::count(),
                'totalAssignedComplaint' => Complaint::where('on_assigned', true)->count(),
                'totalNotAssignedComplaint' => Complaint::where('on_assigned', false)->count(),
                'totalFinishedComplaint' => Complaint::where('on_assigned', true)->where('finished', true)->count(),
                'totalWorkingComplaint' => Complaint::where('on_assigned', true)->where('finished', false)->count()
            ]], 200);
        }
        elseif(strtolower($slug) == 'pegawai') {
            return response(['info' => [
                'totalComplaint' => Complaint::where('user_complaint_id', '=', $user->id)->count(),
                'totalAssignedComplaint' => Complaint::where('user_complaint_id', '=', $user->id)->where('on_assigned', true)->count(),
                'totalNotAssignedComplaint' => Complaint::where('user_complaint_id', '=', $user->id)->where('on_assigned', false)->count(),
                'totalFinishedComplaint' => Complaint::where('user_complaint_id', '=', $user->id)->where('on_assigned', true)->where('finished', true)->count(),
                'totalWorkingComplaint' => Complaint::where('user_complaint_id', '=', $user->id)->where('on_assigned', true)->where('finished', false)->count()
            ]], 200);
        }
        else {
            return response(['info' => [
                'totalComplaint' => Complaint::with(['assigned'])->whereHas('assigned', function($q) use($user) {
                    $q->where('user_perform_id', $user->id);
                })->count(),

                'totalAssignedComplaint' => Complaint::with(['assigned'])->whereHas('assigned', function($q) use($user) {
                    $q->where('user_perform_id', $user->id);
                })->where('on_assigned', true)->count(),

                'totalNotAssignedComplaint' => Complaint::with(['assigned'])->whereHas('assigned', function($q) use($user) {
                    $q->where('user_perform_id', $user->id);
                })->where('on_assigned', false)->count(),
                
                'totalFinishedComplaint' => Complaint::with(['assigned'])->whereHas('assigned', function($q) use($user) {
                    $q->where('user_perform_id', $user->id);
                })->where('on_assigned', true)->where('finished', true)->count(),

                'totalWorkingComplaint' => Complaint::with(['assigned'])->whereHas('assigned', function($q) use($user) {
                    $q->where('user_perform_id', $user->id);
                })->where('on_assigned', true)->where('finished', false)->count()
            ]], 200);
        }


    }
}
