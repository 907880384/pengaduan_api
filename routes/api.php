<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['json.response'],'namespace' => 'Api'], function () {
    
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');


    Route::group(['middleware' => ['auth:api']], function () {   
        Route::get('users', 'UserController@index');
        Route::get('logout', 'AuthController@logout');


        Route::group(['prefix' => 'operational_types'], function () {
            Route::get('/{op_id}', 'OperationalTypeController@show');
            Route::post('/store', 'OperationalTypeController@store');
            Route::put('/update/{op_id}', 'OperationalTypeController@update');
            Route::delete('/delete/{op_id}', 'OperationalTypeController@delete');
        });

    });

});

