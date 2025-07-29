<?php

namespace App\Traits;

use Carbon\Carbon;

trait FormatsDateInputs
{
    public function getDateFormats(): array
    {
        return ['d-m-Y', 'd/m/Y', 'Y-m-d', 'Y/m/d'];
    }
    /**
     * Format the given date input to Y-m-d format.
     *
     * @param  string  $dateInput
     * @return string|null
     */
    protected function toSystemDateFormat($dateInput)
    {
        foreach ($this->getDateFormats() as $format) {
            try {
                $date = Carbon::createFromFormat($format, $dateInput);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                // Skip to the next format
            }
        }

        return null;
    }

    /**
     * User Date Format:
     * Convert Date to User Assigned Date Format
     * @return null or string
     * */
    public function toUserDateFormat($dateInput)
    {
        if(!$dateInput){
            return null;
        }
        try {
                $date = Carbon::parse($dateInput);
                return $date->format(app('company')['date_format']);
            } catch (\Exception $e) {
                //
            }
        return null;
    }
}