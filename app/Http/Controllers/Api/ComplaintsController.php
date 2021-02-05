<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Complaint;
use App\Models\Role;
use App\Models\Assigned;
use App\User;
use App\Models\MobileNotification;
use App\Events\ComplaintsEvent;
use App\Events\AssignedComplaintEvent;
use App\Events\AssignedWorkingComplaintEvent;
use Helper;

class ComplaintsController extends Controller
{
    
    private $page = 10;

    private function sendResponse($msg, $status=200) {
        return response(['message' => $msg], $status);
    }

    public function index()
    {
        $user = Auth::user();
        if(!$user) {
            return $this->sendResponse(Helper::messageResponse()->NOT_ACCESSED, 400);
        }

        $sentences = request()->query('sentences');
        $search = request()->query('search');
        $slug = strtolower($user->roles()->first()->slug);

        $records = Complaint::with(['sender', 'assigned', 'logs', 'types']);

        if($sentences) {
            switch ($sentences) {
                case 'not_assigned':
                    $records = $records->where('is_assigned',  false);
                    break;
                case 'assigned_accepted':
                    $records = $records->where('is_assigned', true)->where('is_finished', false);
                    break;
                case 'finished':
                    $records = $records->where('is_assigned', true)->where('is_finished', true);
                    break;
                default:
                    $records = $records;
                    break;
            }
        }

        
        if($slug == 'pegawai') {
            $records = $records->where('sender_id', $user->id);
        }

        if($slug != 'admin' && $slug != 'pegawai') {
            $records = $records->whereHas('assigned', function($q) use($user) {
                $q->where('executor_id', $user->id);
            });
        }

        if($search && $search != '') {
            $records = $records->where('title', 'like', '%'.$search.'%');
        }
        
        $records = $records->orderBy('updated_at', 'desc')->paginate($this->page); 

        $records->getCollection()->transform(function($query) {
            if($query->assigned != null) {
                $query->executor = \App\User::find($query->assigned->executor_id);
            }
            return $query;
        });    
        
        return response($records, 200);
    }
    
    public function show($id)
    {
        $record = Complaint::with([
            'sender', 
            'assigned', 
            'logs', 
            'types'
        ])->find($id);
        
        $record->executor = ($record->assigned && $record->assigned != null) ? \App\User::find($record->assigned->executor_id) : null;

        if(!$record) {
            return $this->sendResponse(Helper::messageResponse()->COMPLAINT_NOT_FOUND, 404);    
        }

        return response(['result' => $record], 200); 
    }

    public function destroy($id)
    {
        $record = Complaint::find($id);

        if(Auth::user()->roles()->first()->slug === 'admin') {
            $record->delete();
            return $this->sendResponse(Helper::messageResponse()->COMPLAINT_DELETE_SUCCESS, 200);
        }

        return $this->sendResponse(Helper::messageResponse()->NOT_ACCESSED, 400);
        
    }


    public function store(Request $req)
    {
        $slug = strtolower(
            Auth::user()->roles()->first()->slug
        );

        if($slug != 'pegawai') {
            return $this->sendResponse(Helper::messageResponse()->NOT_ACCESSED, 400);
        }

        $req->validate([
            'title' => 'required|string',
            'messages' => 'required',
            'is_urgent' => 'required',
            'type_id' => 'required',
        ]);

        $complaint = Complaint::create([
            'title' => $req->title,
            'messages' => $req->messages,
            'is_urgent' => $req->is_urgent,
            'sender_id' => Auth::user()->id,
            'type_id' => $req->type_id
        ]);

        if(!$complaint) {
            return $this->sendResponse(Helper::messageResponse()->COMPLAINT_CREATE_FAIL, 400);
        }

        $notification = MobileNotification::create([
            'type' => 'CREATE_NEW_COMPLAINT',
            'receiver_id' => 1,
            'messages' => Auth::user()->name . ' menambahkan pengaduan baru',
            'data' => json_encode($complaint, true),
        ]);

        if(!$notification) {
            return $this->sendResponse(Helper::messageResponse()->NOTIFICATION_ADD_FAIL);
        }

        event(new ComplaintsEvent($complaint, 1, $notification));

        return $this->sendResponse(Helper::messageResponse()->COMPLAINT_CREATE_SUCCESS);
    }

    
    public function assignComplaint(Request $req) 
    {   
        $slug = strtolower(
            Auth::user()->roles()->first()->slug
        );

        if($slug !== 'admin') {
            return $this->sendResponse(Helper::messageResponse()->NOT_ACCESSED, 400);
        }

        $req->validate([
            'complaint_id' => 'required',
            'executor_id' => 'required'
        ]);

        $complaint = Complaint::with(['sender', 'assigned', 'logs', 'types'])->find($req->complaint_id);

        if(!$complaint) {
            return $this->sendResponse(Helper::messageResponse()->COMPLAINT_NOT_FOUND, 404);
        }

        if($complaint->is_assigned == true) {
            return $this->sendResponse(Helper::messageResponse()->COMPLAINT_BEFORE_ASSIGNED, 400);
        }
        
        $complaint->is_assigned = true;
        $complaint->save();

        if(!$complaint) {
            return $this->sendResponse(Helper::messageResponse()->COMPLAINT_ASSIGNED_FAIL, 400);
        }

        $assigned = Assigned::create([
            'complaint_id' => $req->complaint_id,
            'executor_id' => $req->executor_id,
            'status_id' => \App\Models\StatusProcess::where('slug', '=', 'mulai')->first()->id
        ]);

        if(!$assigned) {
            return $this->sendResponse(Helper::messageResponse()->COMPLAINT_ASSIGNED_FAIL, 400);
        }

        $mobileNotif = MobileNotification::create([
            'type' => 'COMPLAINT_ASSIGNED',
            'receiver_id' => $req->executor_id,
            'messages' => 'Informasi Pengaduan '.$complaint->title. ' telah ditugaskan',
            'data' => json_encode($complaint, true),
        ]);

        event(new AssignedComplaintEvent($assigned, $req->executor_id, $mobileNotif));
    
        return $this->sendResponse(Helper::messageResponse()->COMPLAINT_ASSIGNED);
        
    }

    public function startWorkComplaint($assignedId)
    {
        $slug = strtolower(Auth::user()->roles()->first()->slug);

        if($slug === 'admin' || $slug === 'pegawai') {
            return $this->sendResponse(Helper::messageResponse()->NOT_ACCESSED, 400);
        }

        $assigned = Assigned::with(['complaint'])->find($assignedId);
        $assigned->is_accepted = true;
        $assigned->start_work = \Carbon\Carbon::now()->toDateTimeString();
        $assigned->status_id = \App\Models\StatusProcess::where('slug', '=', 'dikerjakan')->first()->id;
        $assigned->save();

        if(!$assigned) {
            return $this->sendResponse(Helper::messageResponse()->COMPLAINT_WORK_ACCEPTED_FAIL, 400);
        }

        $complaint = Complaint::with(['sender', 'assigned', 'logs', 'types'])->find($assigned->complaint_id);

        $mobileNotif = MobileNotification::insert([
            [
                'type' => 'COMPLAINT_WORK_ACCEPTED',
                'receiver_id' => $complaint->sender_id,
                'messages' => Auth::user()->name. " Menerima dan melaksanakan pekerjaan sesuai pengaduan",
                'data' => json_encode($complaint, true),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'type' => 'COMPLAINT_WORK_ACCEPTED',
                'receiver_id' => 1,
                'messages' => Auth::user()->name. " Menerima dan melaksanakan pekerjaan sesuai pengaduan",
                'data' => json_encode($complaint, true),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]
        ]);

        event(new AssignedWorkingComplaintEvent(
            $complaint, 
            [$complaint->sender_id, 1],
            $mobileNotif
        ));

        return $this->sendResponse(Helper::messageResponse()->COMPLAINT_WORK_ACCEPTED);
    }

    public function finishWorkComplaint(Request $req)
    {
        
    }
}
