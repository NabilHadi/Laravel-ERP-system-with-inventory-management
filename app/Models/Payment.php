<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    protected $fillable = [
        'payment_type',
        'reference_id',
        'amount',
        'payment_method',
        'payment_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2'
    ];

    /**
     * Get the payment details
     */
    public function details(): HasOne
    {
        return $this->hasOne(PaymentDetail::class);
    }

    /**
     * Get the parent payable model (Sale or Purchase)
     */
    public function payable(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'payment_type', 'reference_id');
    }
}