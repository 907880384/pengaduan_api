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
        
        if(strtolower($slug) === 'admin') {
            return response(['info' => [
                'totalComplaint' => Complaint::count(),
                'totalAssignedComplaint' => Complaint::where('is_assigned', true)->count(),
                'totalNotAssignedComplaint' => Complaint::where('is_assigned', false)->count(),
                'totalFinishedComplaint' => Complaint::where('is_assigned', true)->where('is_finished', true)->count(),
                'totalWorkingComplaint' => Complaint::where('is_assigned', true)->where('is_finished', false)->count()
            ]], 200);
        }
        elseif(strtolower($slug) === 'pegawai') {
            return response(['info' => [
                'totalComplaint' => Complaint::where('sender_id', '=', $user->id)->count(),
                'totalAssignedComplaint' => Complaint::where('sender_id', '=', $user->id)->where('is_assigned', true)->count(),
                'totalNotAssignedComplaint' => Complaint::where('sender_id', '=', $user->id)->where('is_assigned', false)->count(),
                'totalFinishedComplaint' => Complaint::where('sender_id', '=', $user->id)->where('is_assigned', true)->where('is_finished', true)->count(),
                'totalWorkingComplaint' => Complaint::where('sender_id', '=', $user->id)->where('is_assigned', true)->where('is_finished', false)->count()
            ]], 200);
        }
        else {
            return response(['info' => [
                'totalComplaint' => Complaint::with(['assigned'])->whereHas('assigned', function($q) use($user) {
                    $q->where('executor_id', $user->id);
                })->count(),

                'totalAssignedComplaint' => Complaint::with(['assigned'])->whereHas('assigned', function($q) use($user) {
                    $q->where('executor_id', $user->id);
                })->where('is_assigned', true)->count(),

                'totalNotAssignedComplaint' => Complaint::with(['assigned'])->whereHas('assigned', function($q) use($user) {
                    $q->where('executor_id', $user->id);
                })->where('is_assigned', false)->count(),
                
                'totalFinishedComplaint' => Complaint::with(['assigned'])->whereHas('assigned', function($q) use($user) {
                    $q->where('executor_id', $user->id);
                })->where('is_assigned', true)->where('is_finished', true)->count(),

                'totalWorkingComplaint' => Complaint::with(['assigned'])->whereHas('assigned', function($q) use($user) {
                    $q->where('executor_id', $user->id);
                })->where('is_assigned', true)->where('is_finished', false)->count()
            ]], 200);
        }


    }
}
