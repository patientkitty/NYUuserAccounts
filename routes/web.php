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
Route::get('/webUsers', 'EMScontroller@groupDetails');
Route::get('/findUser', 'EMScontroller@findUser');
Route::get('/input','viewcontroller@inputview')->name('input');
Route::post('/create','EMScontroller@createUser');
Route::post('/bulkImportUser','EMScontroller@bulkImportUser');
Route::any('/testWebUser','EMScontroller@testWebUser');
Route::get('/test1','OrgSyncController@test1');
Route::get('/emsTemplate','EMScontroller@template');