<?php

namespace App\Models\Accounts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class AccountTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_date',
        'amount',
        'account_id',
        'credit_amount',
        'debit_amount',
    ];

    /**
     * Get the parent transactions model (user or post).
     */
    public function transactions(): MorphTo
    {
        return $this->morphTo();
    }

}
