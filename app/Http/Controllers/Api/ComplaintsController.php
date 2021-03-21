<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Complaint;
use App\Models\Role;
use App\Models\Assigned;
use App\Models\StatusProcess;
use App\Models\MobileNotification;
use App\Events\ComplaintsEvent;
use App\Events\AssignedComplaintEvent;
use App\Events\AssignedWorkingComplaintEvent;
use App\Events\FinishedComplaintEvent;
use App\User;
use Helper;
use File;
use Storage;

class ComplaintsController extends Controller
{
    
    private $page = 10;

    private function sendResponse($msg, $status=200) {
        return response(['message' => $msg], $status);
    }

    public function index()
    {
        $user = Auth::user();
        $slug = strtolower($user->roles()->first()->slug);

        $dates = request()->has('dates') ? \Carbon\Carbon::parse(request()->query('dates'))->format('Y-m-d') : \Carbon\Carbon::today()->format('Y-m-d');


        if(request()->has('sentences')) {
            $sentences = request()->query('sentences');
        }
        else {
            $sentences = $slug == 'admin' || $slug == 'customer' ? 'not_assigned' : 'assigned_accepted';
        }

        if(!$user) {
            return $this->sendResponse(Helper::messageResponse()->NOT_ACCESSED, 400);
        }

        $records = Complaint::with(['sender', 'assigned', 'logs', 'types'])
            ->whereDate('created_at', '=', $dates);


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

        
        if($slug == 'customer') {
            $records = $records->where('sender_id', $user->id);
        }

        if($slug != 'admin' && $slug != 'customer') {
            $records = $records->whereHas('assigned', function($q) use($user) {
                $q->where('executor_id', $user->id);
            });
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

        if($record->is_finished) {

            if($record->assigned->filepath != '' && $record->assigned->filepath != null) {
                // $path = Storage::url($record->assigned->filepath);
                // $record->assigned->filepath = url($path);

                $record->assigned->filepath = url($record->assigned->filepath);
            }

            
        }

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

    //
    public function store(Request $req)
    {
        $slug = strtolower(
            Auth::user()->roles()->first()->slug
        );

        if($slug != 'customer') {
            return $this->sendResponse(Helper::messageResponse()->NOT_ACCESSED, 400);
        }

        $req->validate([
            'messages' => 'required',
            'type_id' => 'required',
        ]);

        $complaint = Complaint::create([
            'messages' => $req->messages,
            'sender_id' => Auth::user()->id,
            'type_id' => $req->type_id
        ]);

        if(!$complaint) {
            return $this->sendResponse(Helper::messageResponse()->COMPLAINT_CREATE_FAIL, 400);
        }

        $users = User::with('roles')->whereHas('roles', function($q) {
            $q->where('slug', 'admin');
        })->get(['id']);

        $notifData = [];
        foreach ($users as $u) {
            $notifData[] = MobileNotification::create([
                'type' => 'CREATE_NEW_COMPLAINT',
                'receiver_id' => $u->id,
                'messages' => Auth::user()->name . ' menambahkan pengaduan baru',
                'data' => json_encode($complaint, true),
            ]);
        }

        event(new ComplaintsEvent(
            $complaint, 
            "admin", 
            $notifData
        ));

        return $this->sendResponse(
            Helper::messageResponse()->COMPLAINT_CREATE_SUCCESS
        );
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

        $notifData = [];
        $notifData[] = MobileNotification::create([
            'type' => 'COMPLAINT_ASSIGNED',
            'receiver_id' => $req->executor_id,
            'messages' => 'Informasi Pengaduan '.$complaint->title. ' telah ditugaskan',
            'data' => json_encode($complaint, true),
        ]);

        event(new AssignedComplaintEvent(
            $assigned, [$req->executor_id], $notifData
        ));
    
        return $this->sendResponse(Helper::messageResponse()->COMPLAINT_ASSIGNED);
        
    }

    public function startWorkComplaint($assignedId)
    {
        $slug = strtolower(Auth::user()->roles()->first()->slug);

        if($slug === 'admin' || $slug === 'customer') {
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


        $users = User::with('roles')->whereHas('roles', function($q) {
            $q->where('slug', 'admin');
        })->get(['id']);

        $notifData = [];
        $notifData[] = MobileNotification::create([
            'type' => 'COMPLAINT_WORK_ACCEPTED',
            'receiver_id' => $complaint->sender_id,
            'messages' => Auth::user()->name. " Menerima dan melaksanakan pekerjaan sesuai pengaduan",
            'data' => json_encode($complaint, true),
        ]);

        foreach ($users as $u) {
            $notifData[] = MobileNotification::create([
                'type' => 'COMPLAINT_WORK_ACCEPTED',
                'receiver_id' => $u->id,
                'messages' => Auth::user()->name. " Menerima dan melaksanakan pekerjaan sesuai pengaduan",
                'data' => json_encode($complaint, true),
            ]);
        }

        event(new AssignedWorkingComplaintEvent(
            $complaint, 
            array_merge(
                [$complaint->sender_id],
                $users->transform(function($q) {
                    return $q->id;
                })->toArray()
            ),
            $notifData
        ));

        return $this->sendResponse(
            Helper::messageResponse()->COMPLAINT_WORK_ACCEPTED
        );
    }

    public function finishedComplaint(Request $req)
    {
        $slug = strtolower(Auth::user()->roles()->first()->slug);

        if($slug == 'admin' || $slug == 'customer') {
            return $this->sendResponse(Helper::messageResponse()->NOT_ACCESSED, 400);
        }

        $req->validate([
            'assigned_id' => 'required',
            'description' => 'required',
            'file_upload' => 'required',
        ]);

        
        $assigned = Assigned::find($req->assigned_id);

        if(!$assigned) {
            return $this->sendResponse(Helper::messageResponse()->COMPLAINT_NOT_FOUND, 404);
        }


        if(!$req->file('file_upload')) {
            return $this->sendResponse(Helper::messageResponse()->COMPLAINT_NOT_FOUND, 404);
        }

        if($assigned->filename != null || $assigned->filepath != null) {

            if(file_exists(public_path($assigned->filepath))) {
                unlink(public_path($assigned->filepath));
            }

        }

        $filename = 'C'.$assigned->complaint_id . '_' . time(). '_' . $req->file('file_upload')->getClientOriginalName();
        $pathname = $req->file('file_upload')->storeAs('complaints', $filename, 'public');
        
        $assigned->description = $req->description;
        $assigned->filepath = '/storage/' . $pathname;
        $assigned->filename = $filename;
        $assigned->end_work = \Carbon\Carbon::now();
        $assigned->status_id = StatusProcess::where('slug', 'selesai')->first()->id;
        $assigned->attacher_id = Auth::user()->id;

        if(!$assigned->save()) {
            return $this->sendResponse(Helper::messageResponse()->COMPLAINT_FINISHED_FAIL, 400);
        }


        $complaint = Complaint::with([
            'sender', 
            'assigned', 
            'logs', 
            'types'
        ])->find($assigned->complaint_id);
        $complaint->is_finished = true;
        $complaint->save();


        $users = User::with('roles')->whereHas('roles', function($q) {
            $q->where('slug', 'admin');
        })->get(['id']);

        $notifData = [];
        $notifData[] = MobileNotification::create([
            'type' => 'FINISHED_COMPLAINT',
            'receiver_id' => $complaint->sender_id,
            'messages' => Auth::user()->name. " telah menyelesaikan pekerjaan dan melaporkan hasil pekerjaan dengan baik",
            'data' => json_encode($complaint, true),
        ]);

        foreach ($users as $u) {
            $notifData[] = MobileNotification::create([
                'type' => 'FINISHED_COMPLAINT',
                'receiver_id' => $u->id,
                'messages' => Auth::user()->name. " telah menyelesaikan pekerjaan dan melaporkan hasil pekerjaan dengan baik",
                'data' => json_encode($complaint, true),
            ]);
        }

        event(new FinishedComplaintEvent(
            $complaint, 
            array_merge(
                [$complaint->sender_id],
                $users->transform(function($q) {
                    return $q->id;
                })->toArray()
            ), 
            $notifData
        ));
        
        return $this->sendResponse(
            Helper::messageResponse()->COMPLAINT_FINISHED
        );

    }
}
