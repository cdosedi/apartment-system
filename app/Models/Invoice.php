<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_payment_id',
        'invoice_number',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function leasePayment()
    {
        return $this->belongsTo(LeasePayment::class);
    }
}
