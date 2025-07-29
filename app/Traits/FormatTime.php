<?php

namespace App\Traits;

use Carbon\Carbon;

trait FormatTime
{
    /**
     * Format the given time input to H-m-s format.
     *
     * @param  string  $dateTimeInput
     * @return string|null
     */
    protected function toSystemTimeFormat($dateTimeInput)
    {
        if(!$dateTimeInput){
            return null;
        }
        $time = Carbon::parse($dateTimeInput);
        return $time->format('H:i:s');
    }

    /**
     * User Time Format:
     * Convert DateTime to User Assigned Time Format
     * @return null or string
     * */
    public function toUserTimeFormat($dateTimeInput)
    {
        if(!$dateTimeInput){
            return null;
        }
        try {
                $time = Carbon::parse($dateTimeInput);
                
                return app('company')['time_format'] == 24 
                        ? $time->format('H:i:s') 
                        : $time->format('h:i:s A');
            } catch (\Exception $e) {
                //
            }
        return null;
    }
}