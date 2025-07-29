<?php

namespace App\Models\Expenses;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;
use App\Models\Accounts\AccountGroup;
use App\Models\Accounts\AccountTransaction;
use App\Models\User;

class ExpenseCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'account_group_id',
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
    public function group(): BelongsTo
    {
        return $this->belongsTo(AccountGroup::class, 'account_group_id'); // 'parent_id' is the foreign key
    }

    /**
     * Define the relationship between Expense Payment Transaction & Expense table.
     *
     * @return MorphMany
     */
    /*public function accountTransaction(): MorphMany
    {
        return $this->morphMany(AccountTransaction::class, 'transaction');
    }*/
}
