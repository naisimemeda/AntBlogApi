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

    Route::group(['prefix' => 'comment'], function () {
        //评论
        Route::post('store', 'CommentController@store')->name('comment.store');
    });
    //创建文章
    Route::post('store', 'ArticleController@store')->name('article.store');
    //修改文章
    Route::patch('{article}/update', 'ArticleController@update')->name('article.update');
    //收藏文章
    Route::post('favor/{article}', 'ArticleController@favor')->name('article.favor');
    //取消收藏
    Route::delete('disfavor/{article}', 'ArticleController@disfavor')->name('article.disfavor');
    //收藏文章列表
    Route::get('favorites', 'ArticleController@favorites')->name('article.favorites');
    //文章点赞
    Route::get('{article}/like', 'ArticleController@articleLike')->name('article.like');
    //取消点赞
    Route::delete('{article}/dislike', 'ArticleController@dislike')->name('article.dislike');
});


Route::group(['prefix' => 'article'], function () {
    //文章列表
    Route::get('list', 'ArticleController@index')->name('article.list');
    //文章详情
    Route::get('{article}', 'ArticleController@show')->name('article.show');
});

Route::group(['prefix' => 'auth'], function () {
    //获取令牌
    Route::post('login', 'AuthController@login')->name('users.login');
    //注册
    Route::post('register', 'AuthController@store')->name('users.register');
    //退出
    Route::delete('logout', 'AuthController@logout')->name('users.logout');
});
