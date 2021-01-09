<?php

namespace App\Helpers;

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

}