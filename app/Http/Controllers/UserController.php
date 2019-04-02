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

// manage the user activity.
class UserController extends Controller
{
    public function __construct() {
        App::setLocale('en');      
    }

    // To check email exist
    public function Checkemailexist(Request $request){
    	
    	$returnData = FALSE;
    	$postData = $request->all();

    	if(isset($postData['email']) && $postData['email']){

    		$checkEmail = Apiuser::where('email', $postData['email'])->first();
    		if($checkEmail){
    			$returnData = FALSE;
    		}else{
    			$returnData = TRUE;
    		}
    	}else{
    		$returnData = TRUE;
    	}

    	return Response::json($returnData);
    }

    // To add new user from the admin panel
    public function Addnew(Request $request){

    	//global initialise of varaiable
        $ReturnData = array();
        
        //get all post data for login
        $postData = $request->all();

        //proceed for login
        if (isset($postData) && !empty($postData)) {

        	$randomCode = str_random(6);
        	$newUser = new Apiuser();

        	$newUser->email = $postData['email'];
        	$newUser->name = $postData['name'];
        	$newUser->status = 1;
        	$newUser->password = Hash::make($randomCode);
            $newUser->group_id = str_random(6);
        	$newUser->created_at = date('Y-m-d H:i:s');
        	$newUser->save();

        	$Data['email'] = Input::get('email');
            $Data['password'] = $randomCode;
            $Data['name'] = $postData['name'];

            Mail::send('emails.newuser', $Data, function($message) use ($Data) {
                $message->from('noreply@simpleinjection.com', 'Simple Injection App');
                $message->to($Data['email'])->subject('Simple Injection : Welcome');
            });

            $ReturnData['success'] = true;
            $ReturnData['message'] = trans('message.admin.USER_CREATED_SUCCESSFULLY');

        } else {
            $ReturnData['success'] = false;
            $ReturnData['message'] = trans('message.general.GENERAL_ERROR');
        }
        
        //return response
        return $ReturnData;
    }

    // To list the user
    public function Userlist(Request $request){

        $GetEvents = Apiuser::select('id', 'name','group_id', 'email', 'status', DB::raw('DATE_FORMAT(created_at, "%d %b %Y") as created_on'));
            
            if(!empty($request->get('search'))) {
              $GetEvents->where('name', 'like', '%' . $request->get('search') . '%');
            }

        $GetEventsList = $GetEvents->get();

        return Datatables::of($GetEventsList)
            ->addColumn('action', function ($GetEventsList) {
              return '<a href="manage/edituser/'.$GetEventsList->id.'" data-id="'. $GetEventsList->id .'" class="btn blue edit-product"  style=""><i class="fa fa-pencil"></i> Edit</a><a href="manage/addsubuser/'. $GetEventsList->group_id .'" class="btn btn-primary"><i class="fa fa-plus"></i> Add user</a><a href="javascript:void(0);" data-id="'. $GetEventsList->id .'" class="btn red delete-user"><i class="fa fa-trash"></i></a>';
            })
            ->make(true);

    }

    // To delete the user
    public function Deleteuser(Request $request){

        $returnData['success'] = true;
        $deleteUser = Apiuser::find($request->get('id'));
        $deleteUser->status = 0;
        $deleteUser->save();

        return $returnData; 
    }

    // Edit user
    public function Edituser(Request $request){

        
        $returnData['success'] = false;

        $postData = $request->all();

        if(isset($postData) && count($postData) > 0){

            $editUser = Apiuser::find($postData['id']);
            $editUser->name = $postData['user_name'];
            $editUser->email = $postData['user_email'];
            if(isset($postData['status']) && $postData['status'] !=''){
                $editUser->status = $postData['status'];
            }else{
                $editUser->status = 0;
            }
            $editUser->save();

            $returnData['success'] = true;
            $returnData['message'] = trans('message.admin.USER_EDITED_SUCCESS');

        }else{  

            $returnData['success'] = false;
            $returnData['message'] = trans('message.admin.GENERAL_SUCCESS');
        }

        return $returnData;
    }

    // Assign a device.
    public function Assigndevice(Request $request){

        $returnData['success'] = false;
        $postData = $request->all();

        $getUniqueId = Apiuser::find($request->get('id'))->first()->toArray();

        $object = new Deviceassign();
        $object->unique_id = $getUniqueId['group_id'];
        $object->device_id = $postData['device_id'];
        $object->status = 1;
        $object->created_at = date('Y-m-d H:i:s');

        if($object->save()){
            $updateDevice = Device::find($postData['device_id']);
            $updateDevice->is_assigned = 1;
            $updateDevice->save();
        }

        $returnData['success'] = true;
        $returnData['message'] = trans('message.admin.DEVICE_ASSIGNED_SUCCESSFULLY');

        return $returnData;
    }

    // To get the user information
    public function Getuserinfo(Request $request){

        $returnData['success'] = false;

        $postData = $request->all();

        if(isset($postData['id']) && $postData['id'] !=''){

            $getUserInfo = Apiuser::find($postData['id'])->toArray();

            if($getUserInfo){
                $returnData['data']['user'] = $getUserInfo;
            }

            $getDeviceList = Device::select('*')->where('status', 1)->where('is_assigned', 0)->get()->toArray();
            if($getDeviceList){
                $returnData['data']['devicelist'] = $getDeviceList;
            }

        }else{
            $returnData['success'] = false;
        }

        return $returnData;
    }

    // Assign device list.
    public function Assigneddevicelist(Request $request){

        $postData = $request->all();

        $getUniqueId = Apiuser::find($request->get('id'))->toArray();

        $GetEvents = Deviceassign::select('id',DB::raw('(select name from devices where devices.id = device_id) as device_name'), 'status', DB::raw('DATE_FORMAT(created_at, "%d %b %Y") as created_on'));
            
        if(!empty($request->get('search'))) {
            $GetEvents->where('name', 'like', '%' . $request->get('search') . '%');
        }

        $GetEventsList = $GetEvents->where('status', 1)->where('unique_id', $getUniqueId['group_id'])->get();

        return Datatables::of($GetEventsList)
            ->addColumn('action', function ($GetEventsList) {
              return '<a href="javascript:void(0);" data-id="'. $GetEventsList->id .'" class="btn red revoke_device"><i class="glyphicon glyphicon-remove"></i> Revoke access</a>';
        })
        ->make(true);
    }

    // Revoke the access
    public function Revokeaccess(Request $request){

        $returnData['success'] = false;
        $postData = $request->all();

        // Check if it's already assigned then we have to revoke the access.
        if(isset($postData['id']) && $postData['id']!=''){
            
            $revokeAccess = Deviceassign::find($request->get('id'));
            $revokeAccess->status = 0;
            $revokeAccess->save();

            $returnData['success'] = true;
            $returnData['message'] = trans('message.admin.DEVICE_REVOKE_ACCESS_SUCCESS');
        }

        return $returnData;
    }

    public function Addsubuser(Request $request){

        //global initialise of varaiable
        $ReturnData = array();
        
        //get all post data for login
        $postData = $request->all();

        //proceed for login
        if (isset($postData) && !empty($postData)) {

            $randomCode = str_random(6);
            $newUser = new Apiuser();

            $newUser->email = $postData['email'];
            $newUser->name = $postData['name'];
            $newUser->group_id = $postData['group_id'];
            $newUser->status = 1;
            $newUser->password = Hash::make($randomCode);
            $newUser->created_at = date('Y-m-d H:i:s');
            $newUser->save();

            $Data['email'] = Input::get('email');
            $Data['password'] = $randomCode;
            $Data['name'] = $postData['name'];

            Mail::send('emails.newuser', $Data, function($message) use ($Data) {
                $message->from('noreply@simpleinjection.com', 'Simple Injection App');
                $message->to($Data['email'])->subject('Simple Injection : Welcome');
            });

            $ReturnData['success'] = true;
            $ReturnData['message'] = trans('message.admin.USER_CREATED_SUCCESSFULLY');

        } else {
            $ReturnData['success'] = false;
            $ReturnData['message'] = trans('message.general.GENERAL_ERROR');
        }

        //return response
        return $ReturnData;
    }

}
