<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileNotification;
class MobileNotificationController extends Controller
{
    private $page = 10;

    public function findLimit($receiver) {
        $result = MobileNotification::where('receiver_id', $receiver)->where('read_at', null)->limit(5)->get();
        if(!$result) {
            return response()->json(['message' => 'Notification tidak ditemukan'], 404);
        }
        return response()->json(['result' => $result], 200);
    }

    public function read($id) {
        $notification = MobileNotification::find($id);
        $notification->read_at = \Carbon\Carbon::now();
        $notification->save();

        return view('pages.notifications.info', compact('notification'));
    }

    public function show() {
        $records = MobileNotification::where('receiver_id', Auth::user()->id)->paginate($this->page);
        return view('pages.notifications.index', compact('records'));
    }
}
