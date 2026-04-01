<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'full_name', 'email', 'contact_number', 'address',
        'emergency_contact_name', 'emergency_contact_number',
        'status', 'created_by',
    ];

    protected $casts = [
        'status' => 'string',
        'deleted_at' => 'datetime',
    ];

    public function getFullNameAttribute($value)
    {
        return ucwords(strtolower($value));
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function leases()
    {
        return $this->hasMany(Lease::class)->orderBy('start_date', 'desc');
    }

    public function activeLease()
    {
        return $this->hasOne(Lease::class)->where('status', 'active');
    }

    public function hasActiveLease(): bool
    {
        return $this->leases()->where('status', 'active')->exists();
    }

    public function currentRoom()
    {
        return $this->hasOneThrough(
            Room::class,
            Lease::class,
            'tenant_id',
            'id',
            'id',
            'room_id',
        )->where('leases.status', 'active');
    }
}
