<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'sale_date',
        'subtotal',
        'tax',
        'discount',
        'total',
        'payment_status',
        'notes',
        'warehouse_id',
        'paid_amount'
    ];

    protected $dates = [
        'sale_date'
    ];

    protected $casts = [
        'sale_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Get all payments for the sale
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable', 'payment_type', 'reference_id');
    }

    /**
     * Get the remaining amount to be paid
     */
    public function getRemainingAmountAttribute()
    {
        return $this->total - $this->paid_amount;
    }

    /**
     * Check if the sale is fully paid
     */
    public function getIsFullyPaidAttribute()
    {
        return $this->remaining_amount <= 0;
    }
}