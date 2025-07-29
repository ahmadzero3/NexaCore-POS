<?php

namespace App\Models\Expenses;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Expenses\AccountGroup;

use App\Models\Expenses\ExpenseItem;
use App\Models\PaymentTransaction;
use App\Models\Expenses\ExpenseCategory;
use App\Models\Expenses\ExpenseSubcategory;
use App\Models\Accounts\AccountTransaction;

use App\Traits\FormatsDateInputs;


class Expense extends Model
{
    use FormatsDateInputs;

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'expense_category_id',
        'expense_subcategory_id',
        'expense_date',
        'prefix_code',
        'count_id',
        'expense_code',
        'note',
        'round_off',
        'grand_total',
        'paid_amount',
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
     * Use it as formatted_expense_date
     * */
    public function getFormattedExpenseDateAttribute()
    {
        return $this->toUserDateFormat($this->expense_date); // Call the trait method
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
     * Self Account Group name access
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id'); // 'parent_id' is the foreign key
    }

    /**
     * Self Account Group name access
     *
     * @return BelongsTo
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseSubcategory::class, 'expense_subcategory_id'); // 'parent_id' is the foreign key
    }


    /**
     * Expense Items
     *
     * @return BelongsTo
     */
    public function items(): HasMany
    {
        return $this->hasMany(ExpenseItem::class, 'expense_id'); // 'parent_id' is the foreign key
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

    /**
     * Define the relationship between Expense Payment Transaction & Expense table.
     *
     * @return MorphMany
     */
    public function accountTransaction(): MorphMany
    {
        return $this->morphMany(AccountTransaction::class, 'transaction');
    }

    public function getTableCode()
    {
        return $this->expense_code;
    }
}
