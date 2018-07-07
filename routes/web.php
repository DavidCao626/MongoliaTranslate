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
Route::any('/wechat', 'WeChatController@serve');
Route::any('/addmenu', 'WeChatController@addmenu');

Route::Any('CA/OrderNotify', 'JsApiWeiChatController@OrderNotify')->name('OrderNotify');

Route::group(['middleware' => ['web', 'wechat.oauth']], function () {
    Route::Any('pay', 'JsApiWeiChatController@wechatJsapi');
    
    Route::get('/traslate', function () {
        return view('traslate/index');
    });
    Route::group(['prefix' => 'CA'], function () {
       
        Route::Any('OrderCreate', 'JsApiWeiChatController@OrderCreate')->name('OrderCreate');
    });
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => 'sms'], function () {
    Route::Any('send', 'SmsController@index');
});

Route::group(['prefix' => 'sms'], function () {
    Route::Any('xsend', 'SmsController@store');
});


Route::resource('orders', 'OrdersController', ['only' => ['index', 'show', 'create', 'store', 'update', 'edit', 'destroy']]);