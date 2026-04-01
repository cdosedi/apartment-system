<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeasePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_id', 'due_date', 'paid_at', 'amount', 'status', 'electric_bill_amount', 'electric_bill_id', 'carried_over_debt', 'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
        'status' => 'string',
        'electric_bill_amount' => 'decimal:2',

    ];

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'paid' => 'badge-success',
            'overdue' => 'badge-error',
            default => 'badge-warning',
        };
    }

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    public function tenant()
    {
        return $this->hasOneThrough(Tenant::class, Lease::class, 'id', 'id', 'lease_id', 'tenant_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }

    protected static function booted()
    {
        static::retrieved(function ($payment) {
            if ($payment->status === 'pending' && $payment->due_date->isPast() && is_null($payment->paid_at)) {
                $payment->update(['status' => 'overdue']);
            }
        });

        static::updated(function ($payment) {
            if ($payment->status === 'paid' && is_null($payment->paid_at)) {
                $payment->update(['paid_at' => now()]);
            }
        });
    }

    public function markAsPaid(string $paymentMethod): self
    {
        return $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]) ? $this->fresh() : $this;
    }

    public function electricBill()
    {
        return $this->belongsTo(ElectricBill::class);
    }
}
