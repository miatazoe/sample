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


Route::get('/top','HQ_MainController@top_display');

Route::get('/set/place','HQ_MainController@setting_place');
Route::post('/get/place','HQ_MainController@get_place');
Route::post('/store/placelist','HQ_MainController@store_placelist');

Route::get('/add/place','HQ_MainController@add_place');
Route::post('/store/place','HQ_MainController@store_place');
Route::post('/get/judge','HQ_MainController@rain_judge');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
