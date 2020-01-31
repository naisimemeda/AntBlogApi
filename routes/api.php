<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('login','AuthController@login')->name('users.login');
Route::post('users','AuthController@store')->name('users.store');
Route::get('article/list', 'ArticleController@index')->name('article.list');
