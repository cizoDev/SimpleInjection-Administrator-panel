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


Route::any('dologin', 'ApiuserController@Login');
Route::any('forgetpassword', 'ApiuserController@Forgetpassword');
Route::any('devicelist', 'ApiuserController@Devicelist');
Route::any('updateprofile', 'ApiuserController@Updateprofile');
Route::any('changepassword', 'ApiuserController@Changepassword');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
