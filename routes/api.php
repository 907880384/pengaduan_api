<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['json.response'],'namespace' => 'Api'], function () {
    
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');

    Route::group(['middleware' => 'auth:api'], function () {   
        Route::get('users', 'UsersController@index');
        Route::get('logout', 'AuthController@logout');

        /** Roles */
        Route::resource('roles', 'RolesController')->except(['edit', 'create']);
        Route::get('roles/type/complaint', 'RolesController@getOnlyOperationalsRoles');

        /** Status Process */
        Route::resource('status_process', 'StatusProcessController')->except(['edit', 'create']);

        /** Type Of Complaint */
        Route::resource('complaint_types', 'TypeComplainController')->except(['edit', 'create']);
        Route::get('complaint_types/find/{role_id}', 'TypeComplainController@findByRole');

        /** Complaint */
        Route::resource('complaints', 'ComplaintsController')->except(['edit', 'create']);
        Route::post('assigned/complaints', 'ComplaintsController@assignComplaint');
        
        Route::group(['prefix' => 'information'], function () {
            Route::get('/complaints', 'InformationController@getComplaintInfo');
        });


        /** Read Notification Mobile */
        Route::group(['prefix' => 'mobile_notifications'], function () {
            Route::get('/get/read/{id}', 'MobileNotificationController@getAndReadNotification');
        });
    });

});

