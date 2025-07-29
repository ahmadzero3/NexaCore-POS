<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\AppSettings;
use App\Models\Twilio;
use App\Models\Vonage;
use App\Enums\App;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class SmsNotification extends Notification
{
    use Queueable;

    protected $mobile_numbers;

    protected $message;

    protected $appSettingsRecordId;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $validatedData)
    {
        $this->appSettingsRecordId = App::APP_SETTINGS_RECORD_ID->value;
        $this->mobile_numbers = $validatedData['mobile_numbers'];
        $this->message = $validatedData['message'];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [];
    }

    /**
     * Vonage
     * 
     * SMS Gateway
     * 
     * Application link https://dashboard.nexmo.com/
     * 
     * */

    public function toVonage()
    {
        try{

            $smsCredentials = Vonage::find($this->appSettingsRecordId);

            $api_key        = $smsCredentials->api_key;
            $api_secret     = $smsCredentials->api_secret;

            $basic  = new \Vonage\Client\Credentials\Basic($api_key, $api_secret);

            $client = new \Vonage\Client($basic);


            $messages = [];

            $successCount = 0;

            $errorCount = 0;

            $mobile_numbers = explode(',', $this->mobile_numbers);

            foreach ($mobile_numbers as $mobileNumber){

                $response = $client->sms()->send(
                        new \Vonage\SMS\Message\SMS($mobileNumber, 'BRAND_NAME', $this->message)
                );

                $message = $response->current();

                if($message->getStatus() === 0){
                    $successCount++;
                }else{
                    $errorCount++;   
                }
            }

            if ($errorCount === 0) {
                return [
                    'status' => true,
                    'message' => __('message.message_sent_successfully'),
                ];

            } else {
                return [
                    'status' => false,
                    'message' => __('message.failed_to_send_message').'<br>* Successfully sent : '.$successCount.'<br>* Failed Messages : '.$errorCount,
                ];
            }

        }
        catch (\Exception $e) {
            Log::channel('custom')->critical($e->getMessage());

            return [
                    'status'    => false,
                    'message' => $e->getMessage(),
                ];
        }

    }
    /**
     * Twilio
     * Get the sms representation of the notification.
     */

    public function toTwilio()
    {
        try{
            $smsCredentials = Twilio::find($this->appSettingsRecordId);

            $sid    = $smsCredentials->sid;
            $token  = $smsCredentials->auth_token;
            $twilio = new Client($sid, $token);


            $messages = [];

            $mobile_numbers = explode(',', $this->mobile_numbers);

            foreach ($mobile_numbers as $mobileNumber){
                $message = $twilio->messages
                    ->create($mobileNumber, // to
                        array(
                            "from" => $smsCredentials->twilio_number,
                            "body" => $this->message,
                        )
                    );

                $messages[] = $message;
            }

            $successCount = 0;
            $errorCount = 0;

            foreach ($messages as $message) {
                if (in_array($message->status, ['sent', 'queued'])) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            }

            if ($errorCount === 0) {
                return [
                    'status' => true,
                    'message' => __('message.message_sent_successfully'),
                ];

            } else {
                return [
                    'status' => false,
                    'message' => __('message.failed_to_send_message').'<br>* Successfully sent : '.$successCount.'<br>* Failed Messages : '.$errorCount,
                ];
            }

        }
        catch (\Exception $e) {
            Log::channel('custom')->critical($e->getMessage());

            return [
                    'status'    => false,
                    'message' => $e->getMessage(),
                ];
        }

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
