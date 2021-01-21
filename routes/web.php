<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'Web\AuthController@login');
Route::get('login', 'Web\AuthController@login')->name('login');
Route::post('login', 'Web\AuthController@authLogin')->name('login');

//Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    Route::get('dashboard', 'Web\DashboardController@index')->name('dashboard');
    Route::get('logout', 'Web\AuthController@logout')->name('logout');


    Route::resource('categories/complaint', 'Web\TypeComplaintController');

    Route::group(['prefix' => 'users'], function () {
        Route::get('/', 'Web\UsersController@index');
    });

    Route::group(['prefix' => 'roles'], function () {
        Route::get('/', 'Web\RolesController@index');
    });

    Route::group(['prefix' => 'complaints'], function () {
        Route::get('/', 'Web\ComplaintsController@index');
    });

    Route::group(['prefix' => 'activities'], function () {
        Route::get('/', 'Web\ActivitiesController@index');
    });
});
