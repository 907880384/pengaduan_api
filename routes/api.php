<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['json.response'],'namespace' => 'Api'], function () {
    
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');


    Route::group(['middleware' => 'auth:api'], function () {   
        Route::get('users', 'UserController@index');
        Route::get('logout', 'AuthController@logout');


        Route::group(['prefix' => 'operational_types'], function () {
            Route::get('/', 'OperationalTypeController@index');
            Route::get('/{op_id}', 'OperationalTypeController@show');
            Route::post('/store', 'OperationalTypeController@store');
            Route::put('/{op_id}/update', 'OperationalTypeController@update');
            Route::delete('/{op_id}/delete', 'OperationalTypeController@delete');
        });

        Route::group(['prefix' => 'status_process'], function () {
            Route::get('/', 'StatusProcessController@index');
            Route::get('/{sp_id}', 'StatusProcessController@show');
            Route::post('/store', 'StatusProcessController@store');
            Route::put('/{sp_id}/update', 'StatusProcessController@update');
            Route::delete('/{sp_id}/delete', 'StatusProcessController@delete');
        });

    });

});

