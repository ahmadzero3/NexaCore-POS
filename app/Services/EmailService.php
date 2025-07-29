<?php

namespace App\Services;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Notifications\EmailNotification;
use App\Models\SmtpSettings;
use App\Enums\App;

class EmailService{

    private $data;
    protected $appSettingsRecordId;

    public function __construct(){
    	$this->appSettingsRecordId = App::APP_SETTINGS_RECORD_ID->value;
    }
    /**
     * Email API Settings check and Call SMS API's
     * return @return \Illuminate\Http\JsonResponse
    */
    public function send(array $data) : JsonResponse  {
         //Check Email API
        $smtpSettings = SmtpSettings::find($this->appSettingsRecordId);
        if(is_null($smtpSettings) || $smtpSettings->status===0 ){
            return response()->json([
                'status' => false,
                'message' => __('message.there_is_no_active_email_api'),
            ], 409);
        }

        /**
         * Call the email functionality
         * */
        try {
            Mail::send(new \App\Mail\SendEmail($data));

            return response()->json([
                'status' => true,
                'message' => __('message.email_sent_successfully'),
            ]);
        } catch (\Exception $e) {

            Log::channel('custom')->critical($e->getMessage());

            return response()->json([
                'status' => false,
                'message' => __('message.failed_to_send_email').__('app.check_custom_log_file'),
            ],409);
        }
    }
}
