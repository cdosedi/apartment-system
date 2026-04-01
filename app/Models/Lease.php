<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lease extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'room_id',
        'start_date', 'end_date', 'duration_months',
        'monthly_rent', 'status', 'pending_electric_debt',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'monthly_rent' => 'decimal:2',
        'status' => 'string',
        'deleted_at' => 'datetime',
    ];

    public function getDurationDisplayAttribute()
    {
        $months = (int) $this->duration_months;

        if ($months <= 0) {
            return 'N/A';
        }

        if ($months < 12) {
            return $months.($months === 1 ? ' Month' : ' Months');
        }

        $years = floor($months / 12);
        $extraMonths = $months % 12;

        $yearText = $years.($years === 1 ? ' Year' : ' Years');
        $monthText = $extraMonths > 0
            ? ' & '.$extraMonths.($extraMonths === 1 ? ' Month' : ' Months')
            : '';

        return $yearText.$monthText;
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function payments()
    {
        return $this->hasMany(LeasePayment::class)->orderBy('due_date');
    }

    public function pendingPayments()
    {
        return $this->payments()->where('status', 'pending');
    }

    public function overduePayments()
    {
        return $this->payments()->where('status', 'overdue');
    }

    protected static function booted()
    {
        static::saved(function ($lease) {
            if ($lease->status === 'active') {
                $lease->room->update(['status' => 'occupied']);
            } elseif (in_array($lease->status, ['expired', 'terminated'])) {

                if (! $lease->room->leases()->where('status', 'active')->exists()) {
                    $lease->room->update(['status' => 'available']);
                }
            }
        });

        static::deleted(function ($lease) {
            if (! $lease->room->leases()->where('status', 'active')->exists()) {
                $lease->room->update(['status' => 'available']);
            }
        });

        static::retrieved(function ($lease) {
            if ($lease->status === 'active' && $lease->end_date->isPast()) {
                $lease->update(['status' => 'expired']);

                if (! $lease->room->leases()->where('status', 'active')->exists()) {
                    $lease->room->update(['status' => 'available']);
                }
            }
        });

    }
}
