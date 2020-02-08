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
Route::group(['prefix' => 'article', 'middleware' => ['auth:api']], function () {
    //文章列表
    Route::get('list', 'ArticleController@index')->name('article.list');
    //创建文章
    Route::post('store', 'ArticleController@store')->name('article.store');
    //修改文章
    Route::patch('{article}/update', 'ArticleController@update')->name('article.update');

});

Route::group(['prefix' => 'auth'], function () {
    //获取令牌
    Route::post('login', 'AuthController@login')->name('users.login');
    //注册
    Route::post('register', 'AuthController@store')->name('users.register');
    //退出
    Route::delete('logout', 'AuthController@logout')->name('users.logout');
});
