<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'startup_id',
        'amount',
        'status',
        'payment_status',
        'payment_id',
        'payment_method',
        'transaction_id',
        'payment_receipt_no',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function startup()
    {
        return $this->belongsTo(Startup::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->amount);
    }
}
