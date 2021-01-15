<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['json.response'],'namespace' => 'Api'], function () {
    
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');

    Route::get('users', 'UserController@index');
        Route::get('logout', 'AuthController@logout');

        /** Roles */
        Route::group(['prefix' => 'roles'], function () {
            Route::get('/', 'RolesController@index');
            Route::get('/type/complaint', 'RolesController@getOnlyOperationalsRoles');
            Route::get('/{role_id}', 'RolesController@show');
            Route::post('/store', 'RolesController@store');
            Route::put('/{role_id}/update', 'RolesController@update');
            Route::delete('/{role_id}/delete', 'RolesController@delete');
        });

        /** Status Process */
        Route::group(['prefix' => 'status_process'], function () {
            Route::get('/', 'StatusProcessController@index');
            Route::get('/{sp_id}', 'StatusProcessController@show');
            Route::post('/store', 'StatusProcessController@store');
            Route::put('/{sp_id}/update', 'StatusProcessController@update');
            Route::delete('/{sp_id}/delete', 'StatusProcessController@delete');
        });

        /** Type Of Complaint */
        Route::group(['prefix' => 'complaint_types'], function () {
            Route::get('/', 'TypeComplainController@index');
            Route::get('find/{role_id}', 'TypeComplainController@findByRole');
            Route::get('/{ct_id}', 'TypeComplainController@show');
            Route::post('/store', 'TypeComplainController@store');
            Route::put('/{ct_id}/update', 'TypeComplainController@update');
            Route::delete('/{ct_id}/delete', 'TypeComplainController@delete');
        });

        /** Complaint */
        Route::group(['prefix' => 'complaints'], function () {
            Route::get('/', 'ComplaintController@index');
            Route::get('/{id}', 'ComplaintController@show');
            Route::post('/store', 'ComplaintController@store');
            Route::delete('/{ct_id}/delete', 'ComplaintController@delete');
        });


    // Route::group(['middleware' => 'auth:api'], function () {   
        

    // });

});

