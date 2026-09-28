<?php

namespace App\Notifications;

use App\Models\Vehicle;
use Illuminate\Notifications\Notification;

class VehicleMaintenanceAlert extends Notification
{
    public function __construct(public Vehicle $vehicle, public string $reason)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event' => 'maintenance',
            'vehicle_id' => $this->vehicle->id,
            'vehicle_number' => $this->vehicle->number,
            'vehicle_type' => $this->vehicle->type,
            'title' => '🔧 ئاگاداری چاککردنەوەی ئۆتۆمبێل',
            'body' => 'ئۆتۆمبێلی '.$this->vehicle->number.' ('.$this->vehicle->type.') خراوەتە دۆخی چاککردنەوە: '.$this->reason,
        ];
    }
}
