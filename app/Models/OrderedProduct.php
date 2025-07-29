<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\Models\Service;
use App\Models\Order;
use App\Models\JobOrder;
use App\Models\User;
use App\Traits\FormatsDateInputs; // Import your Traits 

class OrderedProduct extends Model
{
    use HasFactory;
    use FormatsDateInputs;

    /**
     * This method calling the Trait FormatsDateInputs
     * @return null or string
     * */
    public function getFormattedStartDateAttribute()
    {
        return $this->toUserDateFormat($this->start_date); // Call the trait method
    }

    /**
     * This method calling the Trait FormatsDateInputs
     * @return null or string
     * */
    public function getFormattedEndDateAttribute()
    {
        return $this->toUserDateFormat($this->end_date); // Call the trait method
    }

    /**
     * OrderedProducts model belongs to Order Model
     *
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the services associated with the ordered products model
     *
     * @return HasMany
     */
    public function service(): HasOne
    {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }

    /**
     * Orderedp Products Tax
     *
     * @return BelongsTo
     */
    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }

    /**
     * Get the services associated with the ordered products model
     *
     * @return HasMany
     */
    public function jobOrder(): HasOne
    {
        return $this->hasOne(JobOrder::class, 'ordered_product_id', 'id');
    }

    /**
     * Get the services associated with the ordered products model
     *
     * @return HasMany
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'assigned_user_id');
    }
}
