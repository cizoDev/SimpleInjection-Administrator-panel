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
use App\User;
use App\Admin;
use App\Device;
use App\Apiuser;
use App\Deviceassign;

class AdminloginController extends BaseController {

    public function __construct() {
        //Artisan::call('cache:clear');  
        App::setLocale('en');      
    }

    //###############################################################
    //Function Name : index
    //Author : Bhargav Bhanderi <bhargav@creolestudios.com>
    //Purpose : To load login view
    //In Params : Void
    //Return : Login HTML View
    //###############################################################
    public function index() {
        //check if user logged in or not
        //if yes then redirec to dashboard
        //print_r(Hash::make('123456'));die;
        if (Auth::guard('admin')->check()) {
            return redirect('manage/dashboard/');
        } else {
            return View::make("login");
        }
    }

    //###############################################################
    //Function Name : Dologin
    //Author : Jigs Virani
    //Purpose : To login the admin
    //In Params : email,password
    //Return : success & redirect to dashboard/Error on same page
    //###############################################################
    public function Dologin(Request $request) {
        
        //global initialise of varaiable
        $ReturnData = array();
        $IsLoggedIn = 0;
        
        //get all post data for login
        $PostData = $request->all();
        //proceed for login
        if (isset($PostData) && !empty($PostData)) {
            //define validator
            $LoginValidator = Validator::make(array(
                        'password' => Input::get('password'),
                        'email' => Input::get('email'),
                            ), array(
                        'password' => 'required',
                        'email' => 'required|email'
            ));
            //check if user already logged in
            if (Auth::check()) {
                $IsLoggedIn = 1;
            }
            //validate the parameters
            if ($LoginValidator->fails()) { //check for validator
                $ReturnData['status'] = '0';
                $ReturnData['message'] = trans('message.general.GENERAL_SUCCESS'); //
            } else if ($IsLoggedIn == '1') {
                //user already logged in then return success
                $ReturnData['status'] = '1';
                $ReturnData['data'] = Auth::guard('admin')->user();
                $ReturnData['message'] = trans('message.admin.GENERAL_SUCCESS');
            } else { //check user's credentials
                $credentials = [
                    "email" => Input::get("email"),
                    "password" => Input::get("password")
                ];
                //check for remember checkbox is checked or not
                $remember = (Input::has('remember')) ? true : false;
                
                // dd(Auth::check());
                if (Auth::guard('admin')->attempt($credentials, $remember)) {
                    $ReturnData['data'] = Auth::guard('admin')->user();
                    $ReturnData['status'] = '1';
                    $ReturnData['message'] = trans('message.admin.GENERAL_SUCCESS');
                } else {
                    $ReturnData['status'] = '0';
                    $ReturnData['message'] = trans('message.admin.LOGIN_ERROR');
                }
            }
        } else {
            $ReturnData['status'] = '0';
            $ReturnData['message'] = Config('constants.GENERAL_ERROR');
        }
        
        //return response
        return $ReturnData;
    }

    //###############################################################
    //Function Name : Forgotpassword
    //Author : Bhargav Bhanderi <bhargav@creolestudios.com>
    //Purpose : To send link for forgot password
    //In Params : email
    //Return : success/error message for emiail sent or not
    //###############################################################
    public function Forgotpassword() {

        //initilise data
        $ReturnData = array();
        $PostData = Input::all();
        if (isset($PostData) && !empty($PostData)) {
            //define validator
            $PasswordValidator = Validator::make(array(
                        'email' => Input::get('email_fp')
                            ), array(
                        'email' => 'required|exists:users,email'
            ));
            //check for validator
            if ($PasswordValidator->fails()) {
                $ReturnData['status'] = '0';
                $ReturnData['message'] = $PasswordValidator->messages()->first();
            } else {
                //update password token and send email for forgot password
                //get email address
                $UserEmail = Input::get('email_fp');
                /* Generate random string with 25 charater. */
                $ForgetToken = str_random(25);
                /* Generate the link with parameter. */
                $EmailParams = array('resetpwd_token' => $ForgetToken, 'email' => base64_encode($UserEmail));
                $ResetURL = action('AdminloginController@Resetpassword', $EmailParams);
                /* Update the user with forgot token. */
                //get user object for admin user
                $UserObject = User::where('email', $UserEmail)->first();
                //update forgot token
                $UserObject->resetpwd_token = $ForgetToken;
                //if token is saved then send email
                if ($UserObject->save()) {
                    //prepare parameter to send in email
                    $Data['username'] = $UserObject->name;
                    $Data['link'] = $ResetURL;
                    $Data['email'] = Input::get('email_fp');
                    Mail::send('emails.forgotpassword', $Data, function($message) use ($Data) {
                        $message->from('noreply@mallyapp.com', 'Mallyapp');
                        $message->to($Data['email'])->subject('Mallyapp : Forgot password');
                    });
                    $ReturnData['status'] = '1';
                    $ReturnData['message'] = Config('constants.admin_messages.FORGOT_PASSWORD_SUCCESS');
                } else {
                    $ReturnData['status'] = '0';
                    $ReturnData['message'] = Config('constants.admin_messages.GENERAL_ERROR');
                }
            }
        } else {
            $ReturnData['status'] = '0';
            $ReturnData['message'] = Config('constants.admin_messages.INVALID_PARAMS');
        }
        //return data
        return $ReturnData;
    }

   
    public function Resetpassword(Request $Request) {

        //get token and email from url
        $ForgotToken = $Request->get('resetpwd_token');
        $Email = base64_decode($Request->get('email'));
        //check if the link is expired or not
        $UserObject = User::where('email', $Email)->where('resetpwd_token', $ForgotToken)->exists();

        if ($UserObject) {
            //user data is found, proceed for reset password
            return view::make("adminsetnewpassword")->with('email', $Email);
        } else {
            //load view for url expired
            return view::make("adminurlexpired");
        }
    }

    
    public function Logout() {
        //logout user
        Auth::logout();
        Auth::guard('admin')->logout();
        //redirect user to login screen
        return redirect('manage/login');
    }

    public function ForgotpasswordApp(Request $request) {

        //initilise data
        $ReturnData = array();
        $PostData = Input::all();
        if (isset($PostData) && !empty($PostData)) {
            //define validator
            $PasswordValidator = Validator::make(array(
                        'email' => Input::get('email_fp')
                            ), array(
                        'email' => 'required|email'
            ));
            //check for validator
            if ($PasswordValidator->fails()) {
                $ReturnData['success'] = '0';
                $ReturnData['message'] = $PasswordValidator->messages()->first();
            } else {
                
                $checkAdmin = Admin::select('*')->where('email', $request->get('email_fp'))->first();
               
                if($checkAdmin){
                        //get email address
                        $UserEmail = Input::get('email_fp');
                        $randomCode = str_random(6);

                        $updatePassword = Admin::find($checkAdmin->id);
                        $updatePassword->password = Hash::make($randomCode);
                        $updatePassword->save();

                        $Data['email'] = Input::get('email_fp');
                        $Data['password'] = $randomCode;
                        Mail::send('emails.forgotpassword', $Data, function($message) use ($Data) {
                            $message->from('noreply@simpleinjection.com', 'Simple Injection');
                            $message->to($Data['email'])->subject('Simple Injection : Forgot password');
                        });

                        $ReturnData['success'] = '1';
                        $ReturnData['message'] = trans('message.admin.FORGOT_PASSWORD_SUCCESS');
                }else{
                     $ReturnData['success'] = '1';
                     $ReturnData['message'] = Config('constant.EMAIL_NOT_FOUND');
                }
            }
        } else {
            $ReturnData['success'] = '0';
            $ReturnData['message'] = Config('constant.GENERAL_ERROR');
        }
        //return data
        return $ReturnData;
    }
    //###############################################################
    //Function Name : Resetpassword
    //Author : Komal Kapadi <komal@creolestudios.com>
    //Purpose : To send reset password link for admin
    //In Params : Email
    //Return : success with email send/error message
    //###############################################################
    public function ResetpasswordApp(Request $Request) {

        //get token and email from url
        $ForgotToken = $Request->get('resetpwd_token');
        $Email = base64_decode($Request->get('email'));
        //check if the link is expired or not
        $UserObject = User::where('email', $Email)->where('resetpwd_token', $ForgotToken)->exists();

        if ($UserObject) {
            //user data is found, proceed for reset password
            return view::make("setnewpasswordapp")->with('email', $Email);
        } else {
            //load view for url expired
            return view::make("adminurlexpired");
        }
    }

    //###############################################################
    //Function Name : Doresetpassword
    //Author : Komal Kapadi <komal@creolestudios.com>
    //Purpose : To reset password for admin
    //In Params : password
    //Return : success/error message
    //###############################################################
    public function DoresetpasswordApp() {
        
        //get all post data for login
        $PostData = Input::all();

        //proceed for login
        if (isset($PostData) && !empty($PostData)) {
            //define validator
            $ResetPassword = Validator::make(array(
                        'password' => Input::get('password'),
                        'password_confirmation' => Input::get('password_confirmation'),
                            ), array(
                        'password' => 'required|min:6|confirmed',
                        'password_confirmation' => 'required|min:6'
            ));
            //validate the parameters
            if ($ResetPassword->fails()) {//check for validator
                $ReturnData['status'] = '0';
                $ReturnData['message'] = $ResetPassword->messages()->first();
            } else { //proceed for reset password
                //make password
                $NewPassword = Hash::make(Input::get('password'));
                //update new password for user
                //get user object for admin user
                $UserObject = User::where('email', $PostData['email'])->first();
                //update forgot token
                $UserObject->password = $NewPassword;
                $UserObject->resetpwd_token = '';

                //if token is saved then send email
                if ($UserObject->save()) {
                    $ReturnData['status'] = '1';
                    $ReturnData['message'] = Config('constants.admin_messages.SUCCESS_PASSWORD_CHANGE');
                } else {
                    $ReturnData['status'] = '0';
                    $ReturnData['message'] = Config('constants.admin_messages.GENERAL_ERROR');
                }
            }
        } else {
            $ReturnData['status'] = '0';
            $ReturnData['message'] = Config('constants.admin_messages.INVALID_PARAMS');
        }
        //return response
        return $ReturnData;
    }

    public function Edit(Request $request){

        $ResponseArray = array();
        $ResponseArray['success'] = false;
        // Procedure call to get states
        $Input = Input::all();
        $RequestData = array(
            'name' => $Input['first_name'],
            'email' => $Input['email']
        );
        $Result = User::where('id', Auth::user()->id)->update($RequestData);
        if ($Result) {
            $ResponseArray['success'] = true;
            $ResponseArray['message'] = 'Updated Successfully.';
        } else {
            $ResponseArray['success'] = true;
            $ResponseArray['data'] = '';
            $ResponseArray['message'] = 'Problem in updating.';
        }
        ## return json response
        return response()->json($ResponseArray);

    }

    public function Changepassword() {

        $ResponseArray = array();
        $ResponseArray['success'] = false;
        // Procedure call to get states
        $Input = Input::all();
        $current_password = Input::get('old_password');
        $new_password = Input::get('new_password');
        $ResultAdmin = User::find(Auth::user()->id);
        if (Hash::check($current_password, $ResultAdmin['password'])) {
            $user_id = Auth::user()->id;
            $new_user_password = User::find($user_id);
            $new_user_password->password = Hash::make($new_password);
            $result = $new_user_password->save();
            if ($result) {
                $ResponseArray['success'] = true;
                $ResponseArray['message'] = Config('constants.admin_messages.PASSWORD_CHANGE_SUCCESS');
            } else {
                $ResponseArray['success'] = false;
                $ResponseArray['message'] = Config('constants.admin_messages.PASSWORD_CHANGE_FAILED');
            }
        } else {
            $ResponseArray['success'] = false;
            $ResponseArray['message'] = Config('constants.admin_messages.INCORRECT_OLD_PASSWORD');
        }
        return response()->json($ResponseArray);
        $Result = User::where('id', Auth::user()->id)->update($RequestData);
        if ($Result) {
            $ResponseArray['success'] = true;
            $ResponseArray['message'] = 'Updated Successfully.';
        } else {
            $ResponseArray['success'] = true;
            $ResponseArray['data'] = '';
            $ResponseArray['message'] = 'Problem in updating.';
        }
        ## return json response
        return response()->json($ResponseArray);
    }

    // To get all users
    public function Getallusers(Request $request){

        $GetEvents = Apiuser::select('id', 'name', 'email','total_scan', DB::raw('DATE_FORMAT(created_at, "%d %b %Y") as created_on'));
            
            $GetEvents->where('status', 1);
            if (!empty($request->get('search'))) {
              $GetEvents->where('name', 'like', '%' . $request->get('search') . '%');
            }

            $GetEventsList = $GetEvents->get();

            return Datatables::of($GetEventsList)
            ->addColumn('action', function ($GetEventsList) {
              return '<a href="javascript:void(0);" data-id="'. $GetEventsList->id .'" class="btn btn-danger delete-currency"><i class="glyphicon glyphicon-remove"></i> Delete</a><a href="manage/user-scan-history/'. $GetEventsList->id .'" data-id="'. $GetEventsList->id .'" class="btn btn-info"><i class="fa fa-barcode"></i> Scan history</a>';
            })
            ->make(true);
    }

    // TO update the profile
    public function Updateprofile(Request $request){

        $responseData['success'] = true;
        $responseData['message'] = trans('message.admin.ADMIN_PROFILE_UPDATE_SUCCESS');
        
        // To update the 
        $updateAdmin = Admin::find($request->get('id'));
        $updateAdmin->name = $request->get('name');
        $updateAdmin->email = $request->get('email');
        $updateAdmin->save();

        return $responseData;
    }

    //To update the password 
    public function Updatepassword(Request $request){

        $responseData['success'] = true;
        $responseData['message'] =  trans('message.admin.PASSWORD_UPDATED_SUCCESS');

        $updateAdmin = Admin::find($request->get('id'));
        $updateAdmin->password = Hash::make($request->get('password'));
        $updateAdmin->save();

        return $responseData;

    }

    // To get the admin information.
    public function Getadmininfo(Request $request){

        $responseData['success'] = true;
        $responseData['data'] = Admin::find(Auth::guard('admin')->id());
       // $responseData['data']['request_new'] = Productrequest::select('*')->where('is_read', 0)->where('status', 1)->count();

		return $responseData;

    }

    public function Getdashboardstats(Request $request){

        $returnData = array();

        $returnData['total_device'] = Device::select('*')->where('status', 1)->count();
        $returnData['total_un_device'] = Device::select('*')->where('status', 1)->where('is_assigned', 0)->count();
        $returnData['total_customer'] = Apiuser::select('*')->where('status', 1)->count();
        $returnData['total_feedback'] = 0;

        return $returnData;

    }

     ## Get dashboard first chart data
    public function Dashboardstateone(Request $request){

        $responseData = Apiuser::select(DB::raw('DATE_FORMAT(created_at, "%b %Y") as Month'), DB::raw('count(id) as visits'))->groupby(DB::raw('MONTH(created_at)'))->get()->toArray();

        //print response.
        return Response::json($responseData, 200, [], JSON_NUMERIC_CHECK);
    }

    // To load the dashboard stats
    public function Dashboardstatesec(Request $request){

        $responseData = Deviceassign::select(DB::raw('DATE_FORMAT(created_at, "%b %Y") as Month'), DB::raw('count(id) as visits'))->groupby(DB::raw('MONTH(created_at)'))->get()->toArray();
        //print response.
        return Response::json($responseData, 200, [], JSON_NUMERIC_CHECK);
    }

}
