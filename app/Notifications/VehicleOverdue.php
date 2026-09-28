<?php

namespace App\Notifications;

use App\Models\VehicleMovement;
use Illuminate\Notifications\Notification;

class VehicleOverdue extends Notification
{
    public function __construct(public VehicleMovement $movement, public int $hoursOut)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $this->movement->loadMissing(['vehicle', 'driver']);

        return [
            'event' => 'overdue',
            'movement_id' => $this->movement->id,
            'vehicle_number' => $this->movement->vehicle?->number,
            'vehicle_type' => $this->movement->vehicle?->type,
            'driver_name' => $this->movement->driver?->name,
            'destination' => $this->movement->destination,
            'departure_time' => optional($this->movement->departure_time)->format('Y-m-d H:i'),
            'hours_out' => $this->hoursOut,
            'title' => '⚠️ ئاگاداری نەگەڕاوە',
            'body' => 'ئۆتۆمبێلی '.$this->movement->vehicle?->number.' لەلایەن '.$this->movement->driver?->name.' زیاتر لە '.$this->hoursOut.' کاتژمێرە لە دەرەوەیە و هێشتا نەگەڕاوەتەوە.',
        ];
    }
}
