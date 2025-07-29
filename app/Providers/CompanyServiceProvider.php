<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;
use App\Enums\App;
use App\Enums\Date;
use App\Enums\Timezone;
use App\Services\CacheService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class CompanyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        if (env('INSTALLATION_STATUS')) {
            // Bind the timezone to a service
            $this->app->singleton('company', function () {

                //Model
                $company = CacheService::get('company'); //Company::find(App::APP_SETTINGS_RECORD_ID->value);

                $timezone = $company ? $company->timezone : Timezone::APP_DEFAULT_TIME_ZONE->value;

                $dateFormat = $company ? $company->date_format : Date::APP_DEFAULT_DATE_FORMAT->value;

                $timeFormat = $company ? $company->time_format : App::APP_DEFAULT_TIME_FORMAT->value;

                $active_sms_api = $company ? $company->active_sms_api : null;

                $isEnableCrm = $company ? $company->is_enable_crm : null;

                return [
                    'name' => $company->name ?? '',
                    'email' => $company->email ?? '',
                    'mobile' => $company->mobile ?? '',
                    'address' => $company->address ?? '',
                    'tax_number' => $company->tax_number ?? '',
                    'timezone' => $timezone,
                    'date_format' => $dateFormat,
                    'time_format' => $timeFormat,
                    'active_sms_api' => $active_sms_api,
                    'number_precision' => $company->number_precision ?? 2,
                    'quantity_precision' => $company->quantity_precision ?? 2,

                    'show_sku' => $company->show_sku ?? 2,//Item Settings, Sidebar-> Settings -> Company ->Item
                    'show_mrp' => $company->show_mrp ?? 2, //Item Settings, Sidebar-> Settings -> Company ->Item
                    'restrict_to_sell_above_mrp' => $company->restrict_to_sell_above_mrp ?? 2, //Item Settings, Sidebar-> Settings -> Company ->Item
                    'restrict_to_sell_below_msp' => $company->restrict_to_sell_below_msp ?? 2, //Item Settings, Sidebar-> Settings -> Company ->Item
                    'auto_update_sale_price' => $company->auto_update_sale_price ?? 2, //Item Settings, Sidebar-> Settings -> Company ->Item
                    'auto_update_purchase_price' => $company->auto_update_purchase_price ?? 2, //Item Settings, Sidebar-> Settings -> Company ->Item
                    'auto_update_average_purchase_price' => $company->auto_update_average_purchase_price ?? 2, //Item Settings, Sidebar-> Settings -> Company ->Item

                    'is_item_name_unique' => $company->is_item_name_unique ?? 2, //Item Settings, Sidebar-> Settings -> Company ->Item
                    'tax_type' => $company->tax_type ?? 2, //Item Settings, Sidebar-> Settings -> Company ->Item

                    'enable_serial_tracking' => $company->enable_serial_tracking ?? 2, //Item Settings, Sidebar-> Settings -> Company ->Item
                    'enable_batch_tracking' => $company->enable_batch_tracking ?? 2, //Item Settings, Sidebar-> Settings -> Company ->Item
                    'is_batch_compulsory' => $company->is_batch_compulsory ?? 2, //Item Settings, Sidebar-> Settings -> Company ->Item
                    'enable_mfg_date' => $company->enable_mfg_date ?? 2, //Item Settings, Sidebar-> Settings -> Company ->Item
                    'enable_exp_date' => $company->enable_exp_date ?? 2, //Item Settings, Sidebar-> Settings -> Company ->Item
                    'enable_color' => $company->enable_color ?? 2, //Item Settings, Sidebar-> Settings -> Company ->Item
                    'enable_size' => $company->enable_size ?? 2, //Item Settings, Sidebar-> Settings -> Company ->Item
                    'enable_model' => $company->enable_model ?? 2, //Item Settings, Sidebar-> Settings -> Company ->Item

                    'show_tax_summary' => $company->show_tax_summary ?? 2, //Print Settings, Sidebar-> Settings -> Company ->Print
                    'state_id' => $company->state_id ?? 2, //Print Settings, Sidebar-> Settings -> Company ->Print
                    'terms_and_conditions' => $company->terms_and_conditions ?? 2, //Print Settings, Sidebar-> Settings -> Company ->Print
                    'show_terms_and_conditions_on_invoice' => $company->show_terms_and_conditions_on_invoice ?? 2, //Print Settings, Sidebar-> Settings -> Company ->Print
                    'show_party_due_payment' => $company->show_party_due_payment ?? 2, //Print Settings, Sidebar-> Settings -> Company ->Print
                    'bank_details' => $company->bank_details ?? 2, //Print Settings, Sidebar-> Settings -> Company ->Print
                    'signature' => $company->signature ?? 2, //Print Settings, Sidebar-> Settings -> Company ->Print
                    'show_signature_on_invoice' => $company->show_signature_on_invoice ?? 2, //Print Settings, Sidebar-> Settings -> Company ->Print
                    'colored_logo' => $company->colored_logo ?? 2, //Print Settings, Sidebar-> Settings -> Company ->Print
                    'is_enable_crm' => $isEnableCrm ?? 2, //Print Settings, Sidebar-> Settings -> Company ->Module
                    'is_enable_carrier' => $company->is_enable_carrier ?? 2,//Print Settings, Sidebar-> Settings -> Company ->Module
                    'is_enable_carrier_charge'  => $company->is_enable_carrier_charge ?? 2, //Print Settings, Sidebar-> Settings -> Company ->General
                    'show_discount' => $company->show_discount ?? 2, //Enable Discount Setting: Sidebar-> Settings -> Company ->General
                    'allow_negative_stock_billing' => $company->allow_negative_stock_billing ?? 2, //Enable Negative Stock Billing - Setting: Sidebar-> Settings -> Company ->General
                    'show_hsn' => $company->show_hsn ?? 2,//Item Settings, Sidebar-> Settings -> Company ->Item
                    'is_enable_secondary_currency' => $company->is_enable_secondary_currency ?? 2,//Item Settings, Sidebar-> Settings -> Company ->General


                ];
            });
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (env('INSTALLATION_STATUS')) {
            try {
                if (Schema::hasTable('company')) {
                    $company = app('company');

                    // Timezone and Carbon
                    date_default_timezone_set($company['timezone']);
                    Carbon::setLocale($company['timezone']);
                    (new Carbon())->settings(['strictMode' => true]);

                    // Mail
                    Config::set('mail.from.address', $company['email']);
                    Config::set('mail.from.name', $company['name']);
                }
            } catch (\Exception $e) {
                // Database not available during build, skip company configuration
            }
        }
    }
}
