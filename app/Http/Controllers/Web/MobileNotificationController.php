<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileNotification;
class MobileNotificationController extends Controller
{
    private $page = 10;

    public function findOneNotifBy($userId, $type) {
        $results = MobileNotification::where('receiver_id',$userId)
            ->where('type', $type)
            ->where('read_at',  null)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        if(!$results) {
            return response()->json(['message' => 'Notification tidak ditemukan'], 404);
        }
        return response()->json(['results' => $results], 200);
    }


    public function findLimit($receiver) {
        $results = MobileNotification::where('receiver_id', $receiver)
            ->where('read_at', null)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        if(!$results) {
            return response()->json(['message' => 'Notification tidak ditemukan'], 404);
        }
        return response()->json(['results' => $results], 200);
    }

    public function read($id) {
        $notification = MobileNotification::find($id);
        $notification->read_at = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        $notification->save();

        return view('pages.notifications.info', compact('notification'));
    }

    public function show() {
        $records = MobileNotification::where('receiver_id', Auth::user()->id)->paginate($this->page);
        return view('pages.notifications.index', compact('records'));
    }
}
