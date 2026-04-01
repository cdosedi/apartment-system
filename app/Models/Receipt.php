<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = ['lease_payment_id', 'receipt_number', 'payment_method', 'amount_paid'];

    protected $casts = [
        'amount_paid' => 'decimal:2',
    ];

    public function leasePayment()
    {
        return $this->belongsTo(LeasePayment::class);
    }
}
