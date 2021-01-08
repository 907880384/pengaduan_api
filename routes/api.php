<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['cors', 'json.response'],'namespace' => 'Api'], function () {
    
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');


    Route::group(['middleware' => ['auth:api']], function () {
        
        Route::get('users', 'UserController@index');

        Route::get('logout', 'AuthController@logout');

    });

});

