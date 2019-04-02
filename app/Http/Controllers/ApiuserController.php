<?php

namespace App\Http\Controllers;
use App;
//load required library by use
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
//load authorization library
use Auth;
use View;
use Hash;
//load session & other useful library
use Carbon\Carbon;
use Datatables;
use Response;
use stdClass;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
//define model
use App\Apiuser;
use App\Admin;
use App\Device;
use App\Deviceassign;

class ApiuserController extends Controller
{
    //
    public function __construct() {
        //Artisan::call('cache:clear');  
        App::setLocale('en');      
    }


    public function Forgetpassword(Request $request){

    	//global declaration
		$ResponseData['success'] =  false;
		//$ResponseData['message'] = Config('message.message.GENERAL_ERROR');
		$ResponseData = array();
		
		//get data from request and process
		$PostData = Input::all();

		if (isset($PostData) && !empty($PostData)) {

            //make validator for facebook
			$ValidateFacebook = Validator::make(array(
				'email' => Input::get('email'),
			), array(
				'email' => 'required:email',
			));
			
			if ($ValidateFacebook->fails()) {
				$ResponseData['message'] = $ValidateFacebook->messages()->first();
				$ResponseData['success'] =  false;
				$ResponseData['data'] = new stdClass();
			}else {
				
				$newPassword = rand ( 10000 , 99999 );

				// To finf the user.
				$getUser = Apiuser::where('email', $request->get('email'))->first();
				
				//print($getUser);die;
				if($getUser){
					$Data['email'] = $request->get('email');
					$Data['password'] = $newPassword;
	                $Data['name'] = $getUser->name;
	            	
	                //To send OTP.
	                Mail::send('emails.apiforgotpassword', $Data, function ($message) use ($Data) {
	                    $message->from('noreply@simpleinjection.com', 'Simple Injection');
	                    $message->to($Data['email'])->subject('Simple Injection : Forgot password');
	                });

	                $update = Apiuser::find($getUser->id);
	                $update->password = Hash::make($newPassword);
	                $update->save();
	                //print_r($newPassword);die;
	                $ResponseData['success'] = true;
	                $ResponseData['messages'] = trans('message.api.PASSWORD_FORGOT_SUCCESS');
				}else{
					$ResponseData['success'] = false;
	                $ResponseData['messages'] = trans('message.api.EMAIL_IS_NOT_AVAILABLE');
				}
			
				}
		} else {
			//print error response
			$ResponseData['success'] =  false;
			$ResponseData['message'] = trans('message.general.GENERAL_ERROR');
			$ResponseData['data'] = new stdClass();
		}
		
		//print response.
		return Response::json($ResponseData, 200, [], JSON_NUMERIC_CHECK);

    }

    public function Login(Request $request){

    	//global declaration
		$ResponseData['success'] =  false;
		$ResponseData = array();
		
		//get data from request and process
		$PostData = Input::all();

		if (isset($PostData) && !empty($PostData)) {

            //make validator for facebook
			$ValidateFacebook = Validator::make(array(
				'email' => Input::get('email'),
				//'lang' => Input::get('lang'),
				'password' => Input::get('password')
			), array(
				'email' => 'required',
				//'lang' => 'required',
				'password' => 'required'
			));
			
			if ($ValidateFacebook->fails()) {
				$ResponseData['message'] = $ValidateFacebook->messages()->first();
				$ResponseData['success'] =  false;
				$ResponseData['data'] = new stdClass();
			}else {

				//echo Hash::make('123456');die;
				if(Auth::guard('web')->attempt(['email' => Input::get("email"), 'password' => Input::get("password")])) {

					$getUserData = Apiuser::where('email', $request->get('email'))->first()->toArray();
					$getUserData1 = Apiuser::GetProfile($getUserData['id']);

					$ResponseData['data'] = $getUserData1['user_data'];
	                $ResponseData['success'] = true;
	                $ResponseData['message'] = trans('message.api.LOGIN_SUCCESS');

	            } else {
	                $ResponseData['success'] = false;
	                $ResponseData['data'] =  new stdClass();
	                $ResponseData['message'] = trans('message.api.LOGIN_ERROR');
	            }
			}
		} else {
            //print error response
			$ResponseData['success'] =  false;
			$ResponseData['message'] = trans('message.general.INVALID_PARAMS');
			$ResponseData['data'] = new stdClass();
		}
		
		//print response.
		return Response::json($ResponseData, 200, [], JSON_NUMERIC_CHECK);
    }

    public function Devicelist(Request $request){

    	//global declaration
		$ResponseData['success'] =  false;
		$ResponseData = array();
		
		//get data from request and process
		$PostData = Input::all();

		if (isset($PostData) && !empty($PostData)) {

            //make validator for facebook
			$ValidateFacebook = Validator::make(array(
				'group_id' => Input::get('group_id')
			), array(
				'group_id' => 'required'
			));
			
			if ($ValidateFacebook->fails()) {
				$ResponseData['message'] = $ValidateFacebook->messages()->first();
				$ResponseData['success'] =  false;
				$ResponseData['data'] = new stdClass();
			}else {

					// Device list.
					$getDeviceList = Deviceassign::select('id','unique_id', 
										DB::raw('(select name from devices where devices.id = device_id) as device_name'),
										DB::raw('(select unique_id from devices where devices.id = device_id) as device_id'))
										->where('status', 1)
										->where(function ($query) use ($PostData) {
											if(isset($PostData['search'])):
										    	$query->where(DB::raw('(select name from devices where devices.id = device_id)'), 'like', '%' . $PostData['search'] . '%');
										    endif;
										})
										->get()
										->toArray();

					// Device list check.
					if($getDeviceList){
						$ResponseData['success'] =  true;
						$ResponseData['message'] = trans('message.general.GENERAL_SUCCESS');
						$ResponseData['data'] = $getDeviceList;
					}else{
						$ResponseData['success'] =  false;
						$ResponseData['message'] = trans('message.api.NO_DEVICE_LIST_AVAILABLE');
						$ResponseData['data'] = new stdClass();
					}
			}
		} else {
            //print error response
			$ResponseData['success'] =  false;
			$ResponseData['message'] = trans('message.general.INVALID_PARAMS');
			$ResponseData['data'] = new stdClass();
		}
		
		//print response.
		return Response::json($ResponseData, 200, [], JSON_NUMERIC_CHECK);
    }

    // To update the current profile.
	public function Updateprofile(Request $request){

		//global declaration
		$ResponseData['success'] =  false;
		$ResponseData['message'] = Config('message.message.GENERAL_ERROR');
		$ResponseData = array();
		
		//get data from request and process
		$PostData = Input::all();

		if (isset($PostData) && !empty($PostData)) {

            //make validator for facebook
			$ValidateFacebook = Validator::make(array(
				'name' => Input::get('name'),
				'user_id' => Input::get('user_id'),
				'email' => Input::get('email'),
				//'username' => Input::get('username'),
				//'mobile' => Input::get('mobile')
			), array(
				//'mobile' => 'required',
				'user_id' => 'required',
				'name' => 'required',
				'email' => 'required',
				//'username' => 'required'
			));
			
			if ($ValidateFacebook->fails()) {
				$ResponseData['message'] = $ValidateFacebook->messages()->first();
				$ResponseData['success'] =  false;
				$ResponseData['data'] = new stdClass();
			}else {
				
				// Create school.
				$addUser = Apiuser::find($request->get('user_id'));

				
				// Set the email address.
				if(isset($PostData['name']) && $PostData['name'] !=''){
					$addUser->name = Input::get('name');
				}
				if(isset($PostData['password']) && $PostData['password'] !=''){
					$addUser->password = Hash::make(Input::get('password'));
				}
				if(isset($PostData['email']) && $PostData['email'] !=''){
					$addUser->email = Input::get('email');
				}
				$addUser->save();

				// To update the user.
				if($addUser){
					$getUserInfo = Apiuser::GetProfile($request->get('user_id'));
					$ResponseData['data'] = $getUserInfo['user_data'];
					$ResponseData['success'] = true;
					$ResponseData['message'] = trans('message.general.PROFILE_UPDATE_SUCCESS');
 				}
			}
		}else{
			//print error response
			$ResponseData['success'] =  false;
			$ResponseData['message'] = Config('constant.INVALID_PARAMS');
			$ResponseData['data'] = new stdClass();
		}

		//print response.
		return Response::json($ResponseData, 200, [], JSON_NUMERIC_CHECK);		
	}


	public function Changepassword(Request $request){

		//global declaration
		$ResponseData['success'] =  false;
		$ResponseData['message'] = Config('message.message.GENERAL_ERROR');
		$ResponseData = array();
		
		//get data from request and process
		$PostData = Input::all();

		if (isset($PostData) && !empty($PostData)) {

            //make validator for facebook
			$ValidateFacebook = Validator::make(array(
				'user_id' => Input::get('user_id'),
				'password' => Input::get('password')
			), array(
				//'mobile' => 'required',
				'user_id' => 'required',
				'password' => 'required'
			));
			
			if ($ValidateFacebook->fails()) {
				$ResponseData['message'] = $ValidateFacebook->messages()->first();
				$ResponseData['success'] =  false;
				$ResponseData['data'] = new stdClass();
			}else {
				
				$update = Apiuser::find($request->get('user_id'));
	            $update->password = Hash::make($request->get('password'));
	            $update->save();

	            $ResponseData['success'] = true;
	            $ResponseData['message'] = "Password updated successfully.";
	            $ResponseData['data'] =  new stdClass();
			}
		}else{
			//print error response
			$ResponseData['success'] =  false;
			$ResponseData['message'] = Config('constant.INVALID_PARAMS');
			$ResponseData['data'] = new stdClass();
		}

		//print response.
		return Response::json($ResponseData, 200, [], JSON_NUMERIC_CHECK);
	}
}
