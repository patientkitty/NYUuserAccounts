<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/show', 'TestController@show');
Route::get('/ems', 'EMScontroller@hello');
Route::get('/buildings', 'EMScontroller@buildings');
Route::get('/webUsers', 'EMScontroller@webUsers');