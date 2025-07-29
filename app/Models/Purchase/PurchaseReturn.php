<?php

namespace App\Models\Purchase;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Party\Party;
use App\Models\Items\ItemTransaction;
use App\Traits\FormatsDateInputs;
use App\Traits\FormatTime;
use App\Models\PaymentTransaction;
use App\Models\Purchase\Purchase;
use App\Models\Accounts\AccountTransaction;
use App\Models\Currency;

class PurchaseReturn extends Model
{
    use HasFactory;

    use FormatsDateInputs;

    use FormatTime;

    protected $table = 'purchase_return';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'return_date',
        'purchase_id',
        'prefix_code',
        'count_id',
        'return_code',
        'reference_no',
        'party_id',
        'state_id',
        'note',
        'round_off',
        'grand_total',
        'paid_amount',
        'currency_id',
        'exchange_rate',
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
     * This method calling the Trait FormatsDateInputs
     * @return null or string
     * Use it as formatted_return_date
     * */
    public function getFormattedReturnDateAttribute()
    {
        return $this->toUserDateFormat($this->return_date); // Call the trait method
    }

    /**
     * This method calling the Trait FormatTime
     * @return null or string
     * Use it as format_created_time
     * */
    public function getFormatCreatedTimeAttribute()
    {
        return $this->toUserTimeFormat($this->created_at); // Call the trait method
    }

    /**
     * Define the relationship between Order and User.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Define the relationship between Order and Party.
     *
     * @return BelongsTo
     */
    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'party_id');
    }

    /**
     * Define the relationship between Item Transaction & Purchase Ordeer table.
     *
     * @return MorphMany
     */
    public function itemTransaction(): MorphMany
    {
        return $this->morphMany(ItemTransaction::class, 'transaction');
    }


    /**
     * Define the relationship between Expense Payment Transaction & Expense table.
     *
     * @return MorphMany
     */
    public function paymentTransaction(): MorphMany
    {
        return $this->morphMany(PaymentTransaction::class, 'transaction');
    }

    public function purchase() : BelongsTo
    {
        //return $this->belongsTo(Purchase::class);
        return $this->belongsTo(Purchase::class, 'reference_no', 'purchase_code');///02-02-2025
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

    public function getTableCode()
    {
        return $this->return_code;
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}
