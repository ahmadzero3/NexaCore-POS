<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Company extends Model
{
    use HasFactory;

    protected $table = 'company';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'address',
        'colored_logo',
        'light_logo',
        'signature',
        'active_sms_api',
        'state_id',
        'bank_details',
        'tax_number',
        'show_discount',
        'allow_negative_stock_billing',
        'is_enable_secondary_currency',
        'is_enable_carrier_charge',
        'restrict_to_sell_above_mrp',
        'restrict_to_sell_below_msp',
        'auto_update_sale_price',
        'auto_update_purchase_price',
        'auto_update_average_purchase_price',
    ];

    /**
     * Insert & update User Id's
     * */
    protected static function boot()
    {
        parent::boot();
        /**
         * created
         * updated
         * cache created in App\Services\CacheService.php
         * */
        static::created(function ($company) {
            Cache::forget('company');
        });
        static::updated(function ($company) {
            Cache::forget('company');
        });
        static::deleted(function ($company) {
            Cache::forget('company');
        });
    }
}
