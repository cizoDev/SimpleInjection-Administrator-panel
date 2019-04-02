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



Route::get('manage', function(){
	return  redirect('manage/login');
});

// Load the login view.
Route::any('manage/login', 'AdminloginController@Index');

Route::group(['prefix' => 'manage'], function () {
    
    //login bypass for the below listed controllers
    Route::resource('login', 'AdminloginController');
    Route::post('dologin', 'AdminloginController@Dologin');
    Route::post('forgotpassword', 'AdminloginController@Forgotpassword');
    Route::get('resetpassword', 'AdminloginController@Resetpassword');
    Route::post('doresetpassword', 'AdminloginController@Doresetpassword');
    Route::post('forgotpasswordapp', 'AdminloginController@ForgotpasswordApp');
    Route::get('resetpasswordapp', 'AdminloginController@ResetpasswordApp');
    Route::post('doresetpasswordapp', 'AdminloginController@DoresetpasswordApp');
    Route::get('logout', 'AdminloginController@Logout');
    Route::any('updateprofile', 'AdminloginController@Updateprofile');
    Route::any('updatepassword', 'AdminloginController@Updatepassword');
    Route::any('getadmininfo', 'AdminloginController@Getadmininfo');
    
    Route::any('addnewuser', 'UserController@Addnew');
    Route::any('addnewsubuser', 'UserController@Addsubuser');
    Route::any('userlist', 'UserController@Userlist');
    Route::any('deleteuser', 'UserController@Deleteuser');
    Route::any('edituser', 'UserController@Edituser');
    Route::any('getuserinfo', 'UserController@Getuserinfo');
    Route::any('assigndevice', 'UserController@Assigndevice');
    Route::any('checkemailexist', 'UserController@Checkemailexist');
    Route::any('assigneddevicelist', 'UserController@Assigneddevicelist');
    Route::any('revokeaccess', 'UserController@Revokeaccess');

    Route::any('adddevice', 'DeviceController@Add');
    Route::any('devicelist', 'DeviceController@List');
    Route::any('editdevice', 'DeviceController@Editdevice');
    Route::any('getdeviceinfo', 'DeviceController@Getdeviceinfo');
    Route::any('deletedevice', 'DeviceController@Delete');

    Route::any('getdashboardstats', 'AdminloginController@Getdashboardstats');

    Route::any('dashboardstateone', 'AdminloginController@Dashboardstateone');
    Route::any('dashboardstatesec', 'AdminloginController@Dashboardstatesec');
    
});

// To load the mastet of the dashboard
Route::middleware('admin')->get('manage/dashboard', function () {
	return view('layouts.master');
});
Route::middleware('admin')->get('manage/users', function () {
	return view('layouts.master');
});
Route::middleware('admin')->get('manage/devices', function () {
	return view('layouts.master');
});
//my-profile
Route::middleware('admin')->get('manage/my-profile', function () {
	return view('layouts.master');
});
Route::middleware('admin')->get('manage/add-user', function () {
	return view('layouts.master');
});
//add-device
Route::middleware('admin')->get('manage/add-device', function () {
    return view('layouts.master');
});
//edituser
Route::middleware('admin')->get('manage/edituser/{id}', function () {
    return view('layouts.master');
});

//editdevice
Route::middleware('admin')->get('manage/editdevice/{id}', function () {
    return view('layouts.master');
});

Route::middleware('admin')->any('manage/feedback', function(){
    return view('layouts.master');
});

//add-sub-user.html
Route::middleware('admin')->any('manage/addsubuser/{id}', function(){
    return view('layouts.master');
});



Route::get('/', function () {
    return view('welcome');
});
