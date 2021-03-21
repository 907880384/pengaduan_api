<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Complaint;
use App\Models\Role;
use App\Models\Assigned;
use App\Models\MobileNotification;
use App\User;
use App\Events\ComplaintsEvent;
use App\Events\AssignedComplaintEvent;
use App\Events\AssignedWorkingComplaintEvent;
use App\Events\FinishedComplaintEvent;
use Helper;
use DataTables;

class ComplaintsController extends Controller
{
    private $page = 10;

    private function sendResponse($msg, $status=200) {
        return response()->json(['message' => $msg], $status);
    }

    public function index()
    {
        $activities = [
            ['type' => 'all', 'value' => 'Seluruh Aktivitas'],
            ['type' => 'not_assigned', 'value' => 'Belum Ditugaskan'],
            ['type' => 'assigned_accepted', 'value' => 'Ditugaskan, Dilaksanakan'],
            ['type' => 'finished', 'value' => 'Pekerjaan Selesai']
        ];

        return view('pages.complaints.index', compact('activities'));
    }

    public function listComplaints(Request $req) {
        $user = Auth::user();
        $slug = strtolower($user->roles()->first()->slug);
        $records = Complaint::with([
            'sender', 
            'assigned', 
            'logs', 
            'types'
        ]);

        $search = '';
        $sentences = 'all';

        if($req->query('search') != null) {
            $search = $req->query('search');
        }

        if($req->query('sentences') != null) {
            $sentences = $req->query('sentences');    
        }

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

        $records = $records->orderBy('updated_at', 'desc')->get();

        return Datatables::of($records)->addIndexColumn()
            ->addColumn('types_name', function($row) {
                return $row->types != null ? $row->types->name : "MENUNGGU";
            })
            ->addColumn('executor', function($row) {
                
                if($row->assigned != null) {
                    return \App\User::find($row->assigned->executor_id)->toArray();
                }
                
                return null;

            })
            ->addColumn('sender_name', function($row) {
                return $row->sender != null ? $row->sender->name : "";
            })
            ->addColumn('action', function($row) use($user) {
                $str = '';

                $str .= '<a href="'.url('complaints/show/detail/' . $row->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> DETAIL </a>';
                
                if(strtolower($user->roles()->first()->slug) == 'admin') {
                    if(!$row->is_assigned && !$row->is_finished) {
                        $str .= '&nbsp;<button class="btn btn-primary btn-sm" onclick="showAssignModal(\'' . $row->id . '\',\'' . $row->type_id . '\')"><i class="fas fa-tag"></i> TUGASKAN</button>';
                    }
                }

                return $str;
            })->rawColumns(['action'])->make(true);

    }


    public function create() {
        $roles = Role::where('id', '!=', 1)->where('id', '!=', 2)->get();
        return view('pages.complaints.create', compact('roles'));
    }

    public function show($id)
    {
        $record = Complaint::with(['sender', 'assigned', 'logs', 'types'])->find($id);

        if(!$record) {
            return $this->sendResponse(Helper::messageResponse()->COMPLAINT_NOT_FOUND, 404);    
        }

        if($record->assigned != null) {
            $record->executor = \App\User::find($record->assigned->executor_id);
        }

        return response(['result' => $record], 200);
    }

    public function showDetail($id) {
        $record = Complaint::with(['sender', 'assigned', 'logs', 'types'])->find($id);

        if($record->assigned != null) {
            $record->executor = \App\User::find($record->assigned->executor_id);
        }

        return view('pages.complaints.detail', compact('record'));
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

    public function showFinished($id) 
    {
        $slug = strtolower(Auth::user()->roles()->first()->slug);

        if($slug != 'customer' && $slug != 'admin') {
            $complaint = Complaint::with([
                'typeComplaint',
                'complainer',
                'assigned',
                'complaintTrackings'
            ])->find($id);

            return view('pages.complaints.finish', compact('complaint'));
        }

        return abort(404);
    
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
