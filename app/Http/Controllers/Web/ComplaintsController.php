<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Complaint;
use App\Models\Role;
use App\Models\Assigned;
use App\Events\ComplaintsEvent;
use App\Events\AssignedComplaintEvent;
use Notification;
use App\Notifications\NotifikasiComplaint;
use App\Notifications\AssignedNotif;

class ComplaintsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $slug = strtolower($user->roles()->first()->slug);
        $records = Complaint::with(['typeComplaint', 'complainer', 'complaintTrackings']);

        if($slug !== 'pegawai' && $slug !== 'admin') {
            $records = $records->whereHas('typeComplaint', function($q) use($user) {
                $q->where('role_id', $user->roles()->first()->id);
            })->where('on_assigned', '=' ,true);
        }
        else {
            if($slug === 'pegawai') {
                $records = $records->where('user_complaint_id', '=', $user->id);
            }
        }

        $records = $records->orderBy('updated_at', 'desc')->paginate(10);       
        return view('pages.complaints.index', compact('records'));
    }

    public function create() {
        $roles = Role::where('id', '!=', 1)->where('id', '!=', 2)->get();
        return view('pages.complaints.create', compact('roles'));
    }

    public function store(Request $req) 
    {
        $admin = \App\User::find(1);
        $req->validate([
            'complaint_type_id' => 'required',
            'messages' => 'required|string',
            'urgent' => 'required',
        ]);

        $complaint = Complaint::create([
            'complaint_type_id' => $req->complaint_type_id,
            'messages' => $req->messages,
            'urgent' => $req->urgent,
            'user_complaint_id' => Auth::user()->id,
        ]);

        if(!$complaint) {
            return response()->json(['message' => 'Gagal buat pengaduan baru'], 400);
        }

        $event = [
            'message' => Auth::user()->name . ' menambahkan pengaduan baru',
            'data' => $complaint
        ];

        event(new ComplaintsEvent($complaint, $admin->id));
        $this->notifCreateComplaint($admin, $complaint);

        return response()->json([
            'message' => 'Pengaduan baru berhasil ditambahkan'
        ], 200);
    }

    public function assignComplaint(Request $req) 
    {    
        $req->validate([
            'id' => 'required',
            'user_perform_id' => 'required'
        ]);

        $complaint = Complaint::find($req->id);
        $complaint->on_assigned = true;
        $complaint->save();

        if(!$complaint) {
            return response()->json(['message' => 'Pengaduan tidak dapat di assign'], 400);
        }

        $assigned = Assigned::create([
            'complaint_id' => $complaint->id,
            'user_perform_id' => $req->user_perform_id,
            'status_id' => \App\Models\StatusProcess::where('slug', '=', 'mulai')->first()->id
        ]);

        if(!$assigned) {
            return response()->json(['message' => 'Assigned ditolak'], 400);
        }
        
        $user = \App\User::find($req->user_perform_id);
        $user->active = true;
        $user->save();

        event(new AssignedComplaintEvent($assigned, $req->user_perform_id));
        $this->notifAssign($req->user_perform_id, $assigned, "Pengaduan ". $complaint->typeComplaint->title . " telah ditugaskan (assigned)");

        return response()->json(["message" => "Pengaduan berhasil di assigned"], 200);
    }

    private function notifCreateComplaint($userReceiveComplaint, $complaint) {
        $userSendComplaint = \App\User::find($complaint->user_complaint_id);
        $msg = [
            'message' => $userSendComplaint->name . ' menambahkan pengaduan baru',
            'data' => [
                "complaint" => $complaint,
                "user_receive" => $userReceiveComplaint
            ]
        ];

        // $user->notify(new \App\Notifications\NotifikasiComplaint($msg));
        Notification::send($userReceiveComplaint, new NotifikasiComplaint($msg));
    }

    private function notifAssign($receiveAssigned, $assigned, $message) {
        $user = \App\User::find($receiveAssigned);

        $msg = [
            'message' => $message,
            'data' => [
                'assigned' => $assigned,
                'receiveAssigned' => $receiveAssigned
            ]
        ];

        Notification::send($user, new AssignedNotif($msg));
    }
}
