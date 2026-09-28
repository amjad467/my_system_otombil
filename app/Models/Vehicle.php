<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = ['number', 'type', 'model', 'description', 'active', 'status'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function movements()
    {
        return $this->hasMany(VehicleMovement::class);
    }

    public function activeMovement()
    {
        return $this->hasOne(VehicleMovement::class)->where('status', 'out')->latestOfMany();
    }

    /**
     * Get computed effective status:
     * - 'inactive' if active is false or status is inactive
     * - 'on_trip' if currently has an open movement (status = 'out')
     * - 'maintenance' if status is maintenance
     * - 'available' otherwise
     */
    public function getEffectiveStatusAttribute(): string
    {
        if (!$this->active || $this->status === 'inactive') {
            return 'inactive';
        }
        if ($this->activeMovement()->exists()) {
            return 'on_trip';
        }
        if ($this->status === 'maintenance') {
            return 'maintenance';
        }
        return 'available';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->effective_status) {
            'on_trip' => 'لە گەشتدایە',
            'maintenance' => 'چاککردنەوە',
            'inactive' => 'ناچالاک',
            default => 'بەردەست',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->effective_status) {
            'on_trip' => 'badge-out',
            'maintenance' => 'badge-maintenance',
            'inactive' => 'badge-inactive',
            default => 'badge-returned',
        };
    }
}
