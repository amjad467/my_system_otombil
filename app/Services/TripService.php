<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMovement;
use App\Notifications\VehicleDeparted;
use App\Notifications\VehicleReturned;
use App\Notifications\VehicleOverdue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class TripService
{
    /**
     * Check if a driver can depart (no open trip and active)
     */
    public function canDriverDepart(int $driverId): bool
    {
        $driver = User::find($driverId);
        if (!$driver || !$driver->active) {
            return false;
        }

        return !VehicleMovement::where('user_id', $driverId)
            ->where('status', 'out')
            ->exists();
    }

    /**
     * Check if a vehicle is available (active, not out, not in maintenance)
     */
    public function isVehicleAvailable(int $vehicleId): bool
    {
        $vehicle = Vehicle::find($vehicleId);
        if (!$vehicle || !$vehicle->active || $vehicle->status === 'maintenance' || $vehicle->status === 'inactive') {
            return false;
        }

        return !VehicleMovement::where('vehicle_id', $vehicleId)
            ->where('status', 'out')
            ->exists();
    }

    /**
     * Get driver status label
     */
    public function driverStatus(User $driver): string
    {
        if (!$driver->active) {
            return 'ناچالاک';
        }

        $hasOpen = VehicleMovement::where('user_id', $driver->id)
            ->where('status', 'out')
            ->exists();
        if ($hasOpen) {
            return 'لە دەرەوەیە';
        }

        $lastReturned = VehicleMovement::where('user_id', $driver->id)
            ->where('status', 'returned')
            ->latest('return_time')
            ->first();

        if ($lastReturned && $lastReturned->return_time && $lastReturned->return_time->isToday()) {
            return 'گەڕاوەتەوە';
        }

        return 'بەردەست';
    }

    /**
     * Get vehicle status label
     */
    public function vehicleStatus(Vehicle $vehicle): string
    {
        if (!$vehicle->active || $vehicle->status === 'inactive') {
            return 'ناچالاک';
        }
        if ($vehicle->status === 'maintenance') {
            return 'چاککردنەوە';
        }

        $hasOpen = VehicleMovement::where('vehicle_id', $vehicle->id)
            ->where('status', 'out')
            ->exists();

        return $hasOpen ? 'لە دەرەوەیە' : 'بەردەست';
    }

    /**
     * Record a new departure
     */
    public function recordDeparture(array $data, User $actor, bool $override = false): VehicleMovement
    {
        return DB::transaction(function () use ($data, $actor, $override) {
            $driverId = $data['user_id'] ?? $actor->id;
            $vehicle = Vehicle::findOrFail($data['vehicle_id']);
            $driver = User::findOrFail($driverId);

            // Validate driver can depart
            if (!$override && !$this->canDriverDepart($driverId)) {
                throw new \Exception('ئەم شۆفێرە هێشتا نەگەڕاوەتەوە و ناتوانێت دەرچوونی نوێ تۆمار بکات.');
            }

            // Validate vehicle is available
            if (!$override && !$this->isVehicleAvailable($data['vehicle_id'])) {
                if ($vehicle->status === 'maintenance') {
                    throw new \Exception('ئەم ئۆتۆمبێلە لە دۆخی چاککردنەوەدایە و ناتوانرێت دەرچوونی بۆ تۆمار بکرێت.');
                }
                throw new \Exception('ئەم ئۆتۆمبێلە لە دەرەوەیە و ناتوانرێت دەرچوونی نوێ بۆ تۆمار بکرێت.');
            }

            $movement = VehicleMovement::create([
                'vehicle_id'     => $data['vehicle_id'],
                'user_id'        => $driverId,
                'destination'    => $data['destination'],
                'purpose'        => $data['purpose'] ?? null,
                'notes'          => $data['notes'] ?? null,
                'departure_time' => Carbon::now(),
                'status'         => 'out',
                'override_by'    => $override ? $actor->id : null,
            ]);

            // Audit log
            AuditLog::create([
                'user_id'     => $actor->id,
                'action'      => $override ? 'departure_overridden' : 'departure_created',
                'model_type'  => VehicleMovement::class,
                'model_id'    => $movement->id,
                'performed_at'=> now(),
                'details'     => [
                    'driver_id'   => $driverId,
                    'driver_name' => $driver->name,
                    'vehicle_id'  => $data['vehicle_id'],
                    'vehicle_no'  => $vehicle->number,
                    'destination' => $data['destination'],
                    'override'    => $override,
                ],
            ]);

            // Notify admins and the driver
            $movement->loadMissing(['vehicle', 'driver']);
            $admins = User::where('role', 'admin')->where('active', true)->get();
            Notification::send($admins, new VehicleDeparted($movement));

            if ($driver->id !== $actor->id) {
                $driver->notify(new VehicleDeparted($movement));
            }

            return $movement;
        });
    }

    /**
     * Record a return
     */
    public function recordReturn(VehicleMovement $movement, User $actor): VehicleMovement
    {
        return DB::transaction(function () use ($movement, $actor) {
            $now = Carbon::now();
            $durationMinutes = $movement->departure_time
                ? (int) $movement->departure_time->diffInMinutes($now)
                : null;

            $movement->update([
                'return_time'      => $now,
                'status'           => 'returned',
                'duration_minutes' => $durationMinutes,
            ]);

            // Audit log
            AuditLog::create([
                'user_id'     => $actor->id,
                'action'      => 'return_recorded',
                'model_type'  => VehicleMovement::class,
                'model_id'    => $movement->id,
                'performed_at'=> now(),
                'details'     => [
                    'driver_id'        => $movement->user_id,
                    'vehicle_id'       => $movement->vehicle_id,
                    'duration_minutes' => $durationMinutes,
                    'destination'      => $movement->destination,
                ],
            ]);

            // Notify admins and driver
            $movement->loadMissing(['vehicle', 'driver']);
            $admins = User::where('role', 'admin')->where('active', true)->get();
            Notification::send($admins, new VehicleReturned($movement));

            if ($movement->driver && $movement->driver->id !== $actor->id) {
                $movement->driver->notify(new VehicleReturned($movement));
            }

            return $movement;
        });
    }

    /**
     * Check active trips and notify if overdue
     */
    public function checkOverdueTrips(): int
    {
        $warningHours = (float) Setting::get('warning_hours', 4);
        $thresholdTime = Carbon::now()->subHours($warningHours);

        $overdueMovements = VehicleMovement::with(['vehicle', 'driver'])
            ->where('status', 'out')
            ->where('departure_time', '<=', $thresholdTime)
            ->get();

        $notifiedCount = 0;
        $admins = User::where('role', 'admin')->where('active', true)->get();

        foreach ($overdueMovements as $mov) {
            $hours = (int) $mov->departure_time->diffInHours(now());
            // Send overdue notification if not already sent recently (within last 4 hours)
            $recentlyNotified = DB::table('notifications')
                ->where('type', VehicleOverdue::class)
                ->where('data', 'like', '%"movement_id":' . $mov->id . '%')
                ->where('created_at', '>=', now()->subHours(4))
                ->exists();

            if (!$recentlyNotified) {
                Notification::send($admins, new VehicleOverdue($mov, $hours));
                $notifiedCount++;
            }
        }

        return $notifiedCount;
    }
}
