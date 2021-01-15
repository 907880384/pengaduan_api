<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Complaint;
use App\User;
use Helper;

class ComplaintController extends Controller
{
    public function index() {
        $page = 20;
        $results = Complaint::with([
            'typeComplaint',
            'complainer',
            'complaintTrackings'
        ])->paginate($page);
        return response($results, 200);
    }

    public function store(Request $req) {
        $req->validate([
            'complaint_type_id' => 'required',
            'messages' => 'required',
            'urgent' => 'required',
            'user_complaint_id' => 'required',
        ]);


        $result = Complaint::create([
            'complaint_type_id' => $req->complaint_type_id,
            'messages' => $req->messages,
            'urgent' => $req->urgent != '' ? $req->urgent : false,
            'finished' => false,
            'user_complaint_id' => $req->user_complaint_id,
        ]);

        if(!$result) {
            return response(['message' => Helper::defaultMessage('Complaint')->CREATE_FAILED], 400);
        }

        return response(['message' => Helper::defaultMessage('Complaint')->CREATE_SUCCESS], 200);
    
    }

    public function show($id) {
        $result = Complaint::with([
            'typeComplaint',
            'complainer',
            'complaintTrackings',
        ])->find($id);

        if(!$result) {
            return response(['message' => Helper::defaultMessage()->FOUND_ERROR,], 404);    
        }

        return response(['result' => $result], 200); 
    }

    public function delete($id) {
        $result = Complaint::find($id);
        $result->delete();
        return response(["message" => Helper::defaultMessage()->DELETE_SUCCESS], 200);
    }
}
