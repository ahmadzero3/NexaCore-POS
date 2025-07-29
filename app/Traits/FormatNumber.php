<?php

namespace App\Traits;

use Illuminate\Support\Number;
use Carbon\Carbon;

trait FormatNumber{

	public function formatWithPrecision($number, $comma = true){
		if($comma){
			return Number::format($number, app('company')['number_precision']);
		}else{
			return str_replace(',', '', Number::format($number, app('company')['number_precision']));
		}
	}

	public function formatQuantity($number){
		return str_replace(',', '', Number::format($number, app('company')['quantity_precision']));
	}

	public function spell($number){
		return Number::spell($number);
	}

}
