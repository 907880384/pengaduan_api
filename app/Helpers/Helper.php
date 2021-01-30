<?php

namespace App\Helpers;

use App\Notifications\NotifikasiComplaint as NotificationComplaint;
use App\Notifications\AssignedNotif as NotificationAssignedComplaint;
use App\Notifications\AssignedWorkingComplaint as NotificationAssignedWorkingComplaint;
use Notification;

class Helper {
    
    public static function defaultMessage($msg='') {
        return (object) [
            "FOUND_ERROR" => "Oppss. your search not found",
            "CREATE_FAILED" => $msg == '' ? "Create Fail" : "$msg Create Fail",
            "CREATE_SUCCESS" => $msg == '' ? "Create Successfully" : "$msg Create Successfully",
            "UPDATE_SUCCESS" => $msg == '' ? "Update Successfully" : "$msg Update Successfully",
            "DELETE_SUCCESS" => $msg == '' ? "Delete Successfully" : "$msg Delete Successfully"
        ];
    }

    public static function setNotification($type, $receive, $result)
    {
        switch ($type) {
            case 'ASSIGNED_COMPLAINT':
                $user = \App\User::find($receive);
                Notification::send($user, new NotificationAssignedComplaint($result));
                break;
            
            case 'CREATE_COMPLAINT':
                $user = \App\User::find($receive);
                Notification::send($user, new NotificationComplaint($result));
                break;

            case 'START_WORKING_ASSIGNED':
                $user = \App\User::whereIn('id', $receive)->get();
                Notification::send($user, new NotificationAssignedWorkingComplaint($result));
            default:
                break;
        }
    }

    

}