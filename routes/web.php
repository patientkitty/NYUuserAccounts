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
Route::post('/addTmp','EMScontroller@addTmp');
Route::post('/bulkImportUser','EMScontroller@bulkImportUser');
Route::post('/bulkImportBooking','EMScontroller@addBookings');
Route::post('/bulkImportReservation','EMScontroller@addReservations');
Route::any('/testWebUser','EMScontroller@testWebUser');
Route::get('/test1','OrgSyncController@test1');
Route::get('/emsUserTemplate','EMScontroller@userTemplate');
Route::get('/emsBookingTemplate','EMScontroller@bookingTemplate');
Route::get('/updateBooking','EMScontroller@updateBooking');

Route::get('/orgsyncView','OrgSyncController@orgsyncView');
Route::get('/getAccountBymail','OrgSyncController@getAccountBymail');
Route::get('/addAccountToClassification','OrgSyncController@addAccountToClassification');
Route::get('/getToken','OrgSyncController@getToken');
Route::get('/getUserByNetID','OrgSyncController@getUserByNetID');
Route::get('/refreshToken','OrgSyncController@refreshToken');
Route::get('/hello', 'EmsController@hello');
Route::get('/addBooking', 'EmsController@addBooking');
Route::get('/getRooms', 'EmsController@getRooms');
Route::get('/emsBookingView','EmsController@emsBookingView');