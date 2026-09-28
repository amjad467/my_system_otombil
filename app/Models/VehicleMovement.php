<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class VehicleMovement extends Model
{
    protected $fillable = [
        'vehicle_id',
        'user_id',
        'destination',
        'purpose',
        'departure_time',
        'return_time',
        'status',
        'notes',
        'override_by',
        'duration_minutes',
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'return_time' => 'datetime',
        'duration_minutes' => 'integer',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function overrideUser()
    {
        return $this->belongsTo(User::class, 'override_by');
    }

    public function getDurationMinutesAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }
        if (!$this->return_time || !$this->departure_time) {
            return null;
        }
        return (int) $this->departure_time->diffInMinutes($this->return_time);
    }

    /**
     * Live duration in minutes (works whether returned or still out)
     */
    public function getCurrentDurationMinutesAttribute(): int
    {
        if ($this->duration_minutes !== null) {
            return $this->duration_minutes;
        }
        if (!$this->departure_time) {
            return 0;
        }
        $endTime = $this->return_time ?: Carbon::now();
        return (int) $this->departure_time->diffInMinutes($endTime);
    }

    /**
     * Alert level for live trips:
     * - 'normal' (0-2 hours)
     * - 'attention' (2-4 hours)
     * - 'warning' (4+ hours)
     */
    public function getOverdueStatusAttribute(): string
    {
        if ($this->status !== 'out') {
            return 'normal';
        }

        $attentionHours = (float) Setting::get('attention_hours', 2);
        $warningHours = (float) Setting::get('warning_hours', 4);

        $hours = $this->current_duration_minutes / 60;

        if ($hours >= $warningHours) {
            return 'warning';
        }
        if ($hours >= $attentionHours) {
            return 'attention';
        }
        return 'normal';
    }

    public function getOverdueLabelAttribute(): string
    {
        return match ($this->overdue_status) {
            'warning' => 'دواکەوتوو (مەترسی)',
            'attention' => 'سەرنج پێویستە',
            default => 'ئاسایی',
        };
    }

    public function getOverdueBadgeClassAttribute(): string
    {
        return match ($this->overdue_status) {
            'warning' => 'badge-danger-glow',
            'attention' => 'badge-warning-glow',
            default => 'badge-normal',
        };
    }
}
