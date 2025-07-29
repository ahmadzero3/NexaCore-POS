<?php

namespace App\Models\Party;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;
use App\Models\Accounts\AccountTransaction;
use App\Traits\FormatsDateInputs;

class PartyTransaction extends Model
{
    use HasFactory;

    use FormatsDateInputs;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_date',
        'party_id',
        'to_pay',
        'to_receive',
    ];

    /**
     * Insert & update User Id's
     * */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id();
            $model->updated_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }
    
    /**
     * Get the parent transactions model (user or post).
     */
    public function transactions(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * This method calling the Trait FormatsDateInputs
     * @return null or string
     * Use it as formatted_transaction_date
     * */
    public function getFormattedTransactionDateAttribute()
    {
        return $this->toUserDateFormat($this->transaction_date); // Call the trait method
    }

    /**
     * Define the relationship between Item Transaction & Items table.
     *
     * @return MorphMany
     */
    public function accountTransaction(): MorphMany
    {
        return $this->morphMany(AccountTransaction::class, 'transaction');
    }

}
