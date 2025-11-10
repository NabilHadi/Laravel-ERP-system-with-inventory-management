<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentDetail extends Model
{
    protected $fillable = [
        'payment_id',
        'bank_name',
        'account_number',
        'check_number',
        'check_date',
        'reference_number',
        'transaction_id'
    ];

    protected $casts = [
        'check_date' => 'date'
    ];

    /**
     * Get the payment that owns the details
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}