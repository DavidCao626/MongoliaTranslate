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
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => 'sms'], function () {
    Route::Any('send', 'SmsController@index');
});

Route::group(['prefix' => 'sms'], function () {
    Route::Any('xsend', 'SmsController@store');
});
Route::Any('/excelToolUpAcc', 'SmsController@excelToolUp');
Route::Any('/state', 'SmsController@state');

