<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['room_number', 'bed_capacity', 'status'];

    protected $casts = [
        'status' => 'string',
    ];

    public function getRoomDisplayAttribute()
    {
        return 'Room '.$this->room_number;
    }

    public function scopeAvailable($query)
    {
        return $query->whereRaw('bed_capacity > (SELECT COUNT(*) FROM leases WHERE leases.room_id = rooms.id AND leases.status = "active")');
    }

    public function scopeOccupied($query)
    {
        return $query->whereRaw('bed_capacity <= (SELECT COUNT(*) FROM leases WHERE leases.room_id = rooms.id AND leases.status = "active")');
    }

    public function currentLease()
    {
        return $this->hasOne(Lease::class)->where('status', 'active');
    }

    public function tenant()
    {
        return $this->hasOneThrough(
            Tenant::class,
            Lease::class,
            'room_id',
            'id',
            'id',
            'tenant_id'
        )->where('leases.status', 'active');
    }

    public static function getAvailableRoomOptions()
    {
        return self::available()
            ->orderBy('room_number', 'asc')
            ->pluck('room_number', 'id')
            ->transform(function ($num, $id) {
                return "Room $num";
            });
    }

    public function leases()
    {
        return $this->hasMany(Lease::class);
    }

    public function activeLeases()
    {
        return $this->hasMany(Lease::class)->where('status', 'active');
    }

    public function getAvailableBedsAttribute()
    {
        return $this->bed_capacity - $this->activeLeases()->count();
    }

    public function scopeOrderByRoomNumber($query)
    {
        return $query->orderByRaw('CAST(room_number AS UNSIGNED) ASC, room_number ASC');
    }

    public function electricBills()
    {
        return $this->hasMany(ElectricBill::class);
    }
}
