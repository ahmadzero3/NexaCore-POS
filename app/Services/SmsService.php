<?php

namespace App\Services;
use Illuminate\Http\JsonResponse;
use App\Notifications\SmsNotification;

class SmsService{

    private $data;

    public function __construct()
    {
    	//
    }
    /**
     * SMS API Settings check and Call SMS API's
     * 
     * return @return \Illuminate\Http\JsonResponse
     * 
    */
    public function send(array $data) : JsonResponse  {

        //Check SMS API
        $activeSmsApi = app('company')['active_sms_api'];
        if(is_null($activeSmsApi)){
            return response()->json([
                'status' => false,
                'message' => __('message.there_is_no_active_sms_api'),
            ], 409);
        }

        $smsNotification = new SmsNotification($data);

        if($activeSmsApi == 'Twilio'){
            $response = $smsNotification->toTwilio();
        }
        else{
            $response = $smsNotification->toVonage();
        }

        if($response['status'] == true){
            return response()->json([
                'status' => true,
                'message' => $response['message'],
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'message' => $response['message'],
            ], 409);
        }

    }
}
