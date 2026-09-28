<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'phone', 'role', 'active', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    public function movements()
    {
        return $this->hasMany(VehicleMovement::class);
    }

    public function activeMovement()
    {
        return $this->hasOne(VehicleMovement::class)->where('status', 'out')->latestOfMany();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    /**
     * Compute effective status for driver:
     * - 'inactive' if not active
     * - 'on_trip' if currently on trip
     * - 'available' otherwise
     */
    public function getEffectiveStatusAttribute(): string
    {
        if (!$this->active) {
            return 'inactive';
        }
        if ($this->activeMovement()->exists()) {
            return 'on_trip';
        }
        return 'available';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->effective_status) {
            'on_trip' => 'لە گەشتدایە',
            'inactive' => 'ناچالاک',
            default => 'بەردەست',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->effective_status) {
            'on_trip' => 'badge-out',
            'inactive' => 'badge-inactive',
            default => 'badge-returned',
        };
    }
}
