<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ElectricBill extends Model
{
    protected $fillable = [
        'room_id',
        'billing_month',
        'total_amount',
    ];

    protected $casts = [
        'billing_month' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function leasePayments(): HasMany
    {
        return $this->hasMany(LeasePayment::class);
    }

    public function getTenantCountAttribute(): int
    {
        return $this->leasePayments()->count();
    }

    public function getShareAmountAttribute(): float
    {
        return $this->tenantCount > 0
            ? round($this->total_amount / $this->tenantCount, 2)
            : 0.00;
    }
}
