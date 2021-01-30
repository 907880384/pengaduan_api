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
    public function getAndReadNotification($id) {
        $notifs = MobileNotification::find($id);

        if(!$notifs) {
            return response(['message' => 'Notification not found'], 404);
        }

        $notifs->read_at = \Carbon\Carbon::now();
        $notifs->save();

        if(!$notifs) {
            return response(['message' => 'Notification cannot be read'], 400);
        }

        if($notifs->type == 'ASSIGNED_COMPLAINT') {
            $data = json_decode($notifs->data, true);
            $assigned = Assigned::with(['user', 'complaint', 'status'])->find($data['data']['id']);
            $notifs->assigned = $assigned;
        }

        return response(['notifs' => $notifs], 200);
    }

    
}
