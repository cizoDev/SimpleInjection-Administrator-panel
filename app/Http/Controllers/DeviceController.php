<?php

namespace App\Http\Controllers;
use App;
use App\Device;
use Datatables;
use DB;

use Illuminate\Http\Request;

// Device controller for admin panel.
class DeviceController extends Controller
{

    // To add the new device
    public function Add(Request $request){

    	//global initialise of varaiable
        $ReturnData = array();
        
        //get all post data for login
        $postData = $request->all();

        //proceed for login
        if (isset($postData) && !empty($postData)) {

        	$newDevice = new Device();
        	
        	$newDevice->name = $postData['name'];
            $newDevice->unique_id = $postData['unique_id'];
        	$newDevice->status = 1;
        	$newDevice->created_at = date('Y-m-d H:i:s');
        	$newDevice->save();

            $ReturnData['success'] = true;
            $ReturnData['data'] = $postData['unique_id'];
            $ReturnData['message'] = trans('message.admin.DEVICE_CREATED_SUCCESSFULLY');

        } else {
            $ReturnData['success'] = false;
            $ReturnData['message'] = trans('message.general.GENERAL_ERROR');
        }
        
        //return response
        return $ReturnData;
    }

    // To list the devices
    public function List(Request $request){

    	$GetEvents = Device::select('id', 'name', 'unique_id', 'status', 'is_assigned', DB::raw('DATE_FORMAT(created_at, "%d %b %Y") as created_on'));
            // Search by string
            if(!empty($request->get('search'))) {
              $GetEvents->where('name', 'like', '%' . $request->get('search') . '%');
            }

            // Unique id
            if(!empty($request->get('unique_id'))) {
              $GetEvents->where('unique_id', 'like', '%' . $request->get('unique_id') . '%');
            }
            $GetEvents->where('status', 1);
            $GetEventsList = $GetEvents->get();

        // return datasets
        return Datatables::of($GetEventsList)
            ->addColumn('action', function ($GetEventsList) {
              return '<a href="manage/editdevice/'.$GetEventsList->id.'" data-id="'. $GetEventsList->id .'" class="btn blue edit-product"  style=""><i class="fa fa-pencil"></i> Edit</a><a href="javascript:void(0);" data-id="'. $GetEventsList->id .'" class="btn red delete-product"><i class="fa fa-trash"></i></a>';
            })
            ->make(true);
    }

    // Edit the device
    public function Editdevice(Request $request){

        $returnData['success'] = false;

        $postData = $request->all();

        if(isset($postData) && count($postData) > 0){

            $editUser = Device::find($postData['device_id']);
            $editUser->name = $postData['name'];
            $editUser->unique_id = $postData['unique_id'];
            $editUser->save();

            $returnData['success'] = true;
            $returnData['message'] = trans('message.admin.DEVICE_UPDATED_SUCCESSFULLY');

        }else{
            $returnData['success'] = false;
            $returnData['message'] = trans('message.admin.GENERAL_SUCCESS');
        }

        return $returnData;
    }

    // Get the device info
    public function Getdeviceinfo(Request $request){

        $returnData['success'] = false;

        $postData = $request->all();

        if(isset($postData['id']) && $postData['id'] !=''){

            $getUserInfo = Device::find($postData['id'])->toArray();

            if($getUserInfo){
                $returnData['data']['device'] = $getUserInfo;
            }
        }else{
            $returnData['success'] = false;
        }

        return $returnData;
    }

    // Delete device
    public function Delete(Request $request){

        $returnData['success'] = true;

        $deleteUser = Device::find($request->get('id'));
        $deleteUser->status = 0;
        $deleteUser->save();

        return $returnData; 
    }

}
