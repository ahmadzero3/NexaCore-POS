<?php

namespace App\Observers;

use App\Models\Twilio;
use App\Models\AppSettings;

class TwilioObserver
{
    /**
     * Handle the Twilio "created" event.
     */
    public function created(Twilio $twilio): void
    {
        //
    }

    /**
     * Handle the Twilio "updated" event.
     */
    public function updated(Twilio $twilio): void
    {

        /*$settings = AppSettings::find(2);
        $settings->active_sms_api = 2;
        $settings->save();*/

    }

    /**
     * Handle the Twilio "deleted" event.
     */
    public function deleted(Twilio $twilio): void
    {
        //
    }

    /**
     * Handle the Twilio "restored" event.
     */
    public function restored(Twilio $twilio): void
    {
        //
    }

    /**
     * Handle the Twilio "force deleted" event.
     */
    public function forceDeleted(Twilio $twilio): void
    {
        //
    }
}
