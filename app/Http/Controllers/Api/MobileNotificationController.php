<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileNotification;
use App\User;
use App\Models\Assigned;
use App\Models\Complaint;
use App\Events\ReadNotificationEvent;
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

            if($record->save()) {
                event(new ReadNotificationEvent($record, Auth::user()->id));
            }            
        }



        return response(['result' => $record]);
    }

    public function countUnread() {
        if(Auth::user()) {
            $records = MobileNotification::where('receiver_id', Auth::user()->id)
                ->where('read_at', '=', null)->count();

            return response(['total' => $records]);
        }
        return $this->sendResponse(Helper::messageResponse()->NOT_ACCESSED, 400);   
    }

    public function findOneNotifBy($userId, $type) {
        $notif = MobileNotification::where('receiver_id', $userId)
            ->where('type', $type)
            ->where('read_at', null)
            ->orderBy('created_at', 'desc')
            ->first();

        if(!$notif) {
            return response(['message' => 'Notification tidak ditemukan'], 404);
        }
        
        return response($notif, 200);
    }

    
}
