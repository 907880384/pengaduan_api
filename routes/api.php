<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['json.response'],'namespace' => 'Api'], function () {
    
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');

    Route::group(['middleware' => 'auth:api'], function () {   
        Route::get('logout', 'AuthController@logout');

        /** Users */
        Route::get('users', 'UsersController@index');
        Route::post('profile/user', 'UsersController@profile');
        Route::get('info/user', 'UsersController@getInfo');
        Route::post('users/change/password', 'UsersController@changePassword');
        
        /** Roles */
        Route::resource('roles', 'RolesController')->except(['edit', 'create']);
        Route::get('operational/roles', 'RolesController@getOperationalRoles');

        /** Status Process */
        Route::resource('status_process', 'StatusProcessController')->except(['edit', 'create']);

        /** Complaint */
        Route::resource('complaints', 'ComplaintsController')->except(['edit', 'create']);
        Route::post('assigned/complaints', 'ComplaintsController@assignComplaint');
        Route::get('accept/assigned/{assignedId}/complaints', 'ComplaintsController@startWorkComplaint');
        Route::post('finished/complaint', 'ComplaintsController@finishedComplaint');
        
        /** Read Notification Mobile */
        Route::group(['prefix' => 'mobile_notifications'], function () {
            Route::get('/show/all', 'MobileNotificationController@showAll');
            Route::get('/read/{notifId}', 'MobileNotificationController@readById');
            Route::get('/count/unread', 'MobileNotificationController@countUnread');
        });


        /** Product */
        Route::resource('products', 'ProductController')->except(['edit', 'store', 'create', 'update', 'destroy']);


        /** Orders */
        Route::get('orders', 'OrderController@index');
        Route::post('orders/add/cart', 'OrderController@addCartOrder');

        Route::get('/information/complaints', 'InformationController@getComplaintInfo');

    });

});

