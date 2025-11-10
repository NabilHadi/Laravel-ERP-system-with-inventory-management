<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'reference_number',
        'type',
        'account_id',
        'amount',
        'entry_type',
        'description'
    ];

    protected $dates = [
        'date'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}