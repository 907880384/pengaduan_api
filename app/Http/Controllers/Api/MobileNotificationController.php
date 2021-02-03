<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileNotification;
use App\User;
use App\Models\Assigned;
use App\Models\Complaint;
use Auth;

class MobileNotificationController extends Controller
{
    private $page = 10;

    private function sendResponse($msg, $status=200) {
        return response(['message' => $msg], $status);
    }

    public function showAll()
    {
        if(Auth::user()) {
            $is_read = request()->query('is_read');
            $records = MobileNotification::where('receiver_id', Auth::user()->id);
            
            if(isset($is_read) && $is_read === 'false') {
                $records = $records->where('read_at', '=', null);
            }
            else {
                $records = $records->where('read_at', '!=', null);
            }
            $records = $records->orderBy('updated_at', 'desc')->paginate($this->page);
            return response($records);
        }

        return $this->sendResponse(Helper::messageResponse()->NOT_ACCESSED, 400);    
    }

    public function readById($id) {
        $record = MobileNotification::find($id);

        if(!$record) {
            return $this->sendResponse(Helper::messageResponse()->NOTIFICATION_NOT_FOUND, 404);
        }

        if($record->read_at == null) {
            $record->read_at = \Carbon\Carbon::now();
            $record->save();
        }

        return response(['result' => $record]);
    }
    
}
