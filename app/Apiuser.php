<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Apiuser extends Model
{
    //
    protected $fillable = [
        'name','email', 'password','created_at'
    ];
    
    //Define the table name
    protected $table = 'users';

    // To profile of users
    public static function GetProfile($UserId) {

        //global declaraton.
        $ReturnData = array();

        $queryUser = DB::table('users')
                    ->select('*')
                    ->where('id', $UserId)
                    ->first();

        $userData = json_decode(json_encode($queryUser), true);
        
        // To user data
        if ($userData) {
            
            $ReturnData['status'] = true;
            $ReturnData['user_data'] = $userData;
        } else {
            $ReturnData['status'] = false;
        }
        
        return $ReturnData;
    }

    // To send the push notification.
    public static function Sendpushtouser($params){

        $returnData['success'] = false;

        try{
            ob_start();
            $url = "https://fcm.googleapis.com/fcm/send";
            $token = $params['token'];
            $serverKey = 'AIzaSyCACCM_k89IasuSl-EWmODvyBBT0u-z3G0';

            $title = $params['title'];
            $body = $params['body'];
            
            $notification = array('title' => $title , 'text' => $body, 'sound' => 'default', 'badge' => '1');
            $arrayToSend = array('to' => $token, 'notification' => $notification, 'priority'=>'high');
            $json = json_encode($arrayToSend);
            
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: key='. $serverKey;
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, false );
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
            //Send the request
            $response = curl_exec($ch);
            
            curl_close($ch);
            ob_end_clean();
            $returnData['data'] = $response;
        }catch(Exception $e){
            echo "";
        }
        
        return $returnData;
    }

}
