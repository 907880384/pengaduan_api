<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Notification;


class NotificationController extends Controller
{
    public function getNotifications($id)
    {
        $user = \App\User::find($id);
        return $user->unreadNotifications()->limit(5)->get()->toArray();
    }

    public function readNotificationComplaint($id, $userId) {
        $user = \App\User::find($userId);
        $notification = $user->notifications->find($id);

        if($notification) {
            $notification->markAsRead();
            return view('pages.notifications.complaint', compact('notification'));
        }
        else {
            return abort(404);
        }

        
    }

    public function readNotificationAssigned($id, $userId)
    {
        $user = \App\User::find($userId);
        $notification = $user->notifications->find($id);

        if($notification) {
            $notification->markAsRead();
            return view('pages.notifications.assigned', compact('notification'));
        }
        else {
            return abort(404);
        }
    }

    public function readNotificationAssignedWorking($id, $userId)
    {
        $user = \App\User::find($userId);
        $notification = $user->notifications->find($id);

        if($notification) {
            $notification->markAsRead();
            return view('pages.notifications.work_complaint', compact('notification'));
        }
        else {
            return abort(404);
        }
    }
}
