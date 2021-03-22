<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'Web\AuthController@login');
Route::get('login', 'Web\AuthController@login')->name('login');
Route::post('login', 'Web\AuthController@authLogin')->name('login');


Route::group(['middleware' => ['auth']], function () {
    Route::get('/', 'Web\DashboardController@index')->name('dashboard');
    Route::get('dashboard', 'Web\DashboardController@index')->name('dashboard');

    Route::get('logout', 'Web\AuthController@logout')->name('logout');

    /** Notification */
    // Route::get('notification/get/by/{user}', 'Web\NotificationController@getNotifications');

    // Route::get('notification/read/complaint/{id}/user/{userId}', 'Web\NotificationController@readNotificationComplaint');

    // Route::get('notification/read/assigned/{id}/user/{userId}', 'Web\NotificationController@readNotificationAssigned');

    // Route::get('notification/read/assigned/working/complaint/{id}/user/{userId}', 'Web\NotificationController@readNotificationAssignedWorking');


    /** Type Complaint */
    Route::resource('categories/complaint', 'Web\TypeComplaintController')->except(['show']);
    Route::get('categories/complaint/roles/{role}', 'Web\TypeComplaintController@getTypeByRole');

    /** Users */
    Route::resource('users', 'Web\UsersController')->except(['edit', 'update']);
    Route::get('users/roles/{id}', 'Web\UsersController@getUserByRole');
    Route::get('users/view/upload', 'Web\UsersController@viewUploadUser');
    Route::post('upload/file/users', 'Web\UsersController@uploadUserFile');

    /** Roles */
    Route::resource('roles', 'Web\RolesController')->except(['show','edit', 'create', 'destroy', 'update', 'store']);

    /** Complaints */
    Route::resource('complaints', 'Web\ComplaintsController');
    Route::get('complaints/show/detail/{id}', 'Web\ComplaintsController@showDetail');
    Route::post('assigned/complaints', 'Web\ComplaintsController@assignComplaint');
    Route::get('accept/assigned/{assignedId}/complaints', 'Web\ComplaintsController@startWorkComplaint');
    Route::get('show/finished/working/complaint/{complaint}', 'Web\ComplaintsController@showFinished');
    Route::post('finish/working/complaint', 'Web\ComplaintsController@finishWorkComplaint');


    /** Products */
    Route::resource(
        'products', 
        'Web\ProductController', 
        ['only' => ['index', 'create', 'show', 'store',   'edit', 'destroy']]
    );

    Route::post('products/{id}', 'Web\ProductController@update');

    /** Orders */
    Route::resource(
        'orders', 
        'Web\OrderController', 
        ['only' => ['index', 'show']]
    );
    Route::get('count/new/orders', 'Web\OrderController@countNewOrder');
    Route::get('orders/agreed/{id}', 'Web\OrderController@agreed');
    Route::post('orders/disagree', 'Web\OrderController@disagree');

    /** Activities */
    Route::group(['prefix' => 'activities'], function () {
        Route::get('/', 'Web\ActivitiesController@index');
    });

    Route::group(['prefix' => 'mobile_notification'], function () {
        Route::get('/find/{user}/type/{type}/notification', 'Web\MobileNotificationController@findOneNotifBy');

        Route::get('/find/{receiver}/limit', 'Web\MobileNotificationController@findLimit');
        Route::get('/show', 'Web\MobileNotificationController@show');
        Route::get('/read/{notifId}', 'Web\MobileNotificationController@read');
    });

    /** Visitor (Buku Tamu) */
    Route::resource('visitors', 'Web\VisitorController');
    Route::get('visitors/exit/{id}', 'Web\VisitorController@exitVisitor');


    /** Datatable */
    Route::get('list/users', 'Web\UsersController@listUsers')->name('list.users');
    Route::get('list/complaints', 'Web\ComplaintsController@listComplaints')->name('list.complaints');
    Route::get('list/visitors', 'Web\VisitorController@listVisitors')->name('list.visitors');
    Route::get('list/orders', 'Web\OrderController@listOrder')->name('list.orders');
});
