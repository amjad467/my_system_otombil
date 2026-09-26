<?php

namespace App\Notifications;

use App\Models\VehicleMovement;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class VehicleReturned extends Notification
{
    public function __construct(public VehicleMovement $movement)
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
            'event' => 'returned',
            'movement_id' => $this->movement->id,
            'vehicle_number' => $this->movement->vehicle->number,
            'vehicle_type' => $this->movement->vehicle->type,
            'driver_name' => $this->movement->driver->name,
            'destination' => $this->movement->destination,
            'return_time' => optional($this->movement->return_time)->format('Y-m-d H:i'),
            'title' => 'گەڕانەوەی ئۆتۆمبێل',
            'body' => 'ئۆتۆمبێلی '.$this->movement->vehicle->number.' لەلایەن '.$this->movement->driver->name.' گەڕایەوە.',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('گەڕانەوەی ئۆتۆمبێل')
            ->line('ئۆتۆمبێلی '.$this->movement->vehicle->number.' لەلایەن '.$this->movement->driver->name.' گەڕایەوە.');
    }
}
