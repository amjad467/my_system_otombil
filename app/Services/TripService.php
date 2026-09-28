<?php
namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMovement;
use App\Notifications\VehicleDeparted;
use App\Notifications\VehicleReturned;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class TripService
{
    /**
     * Check if a driver can depart (no open trip)
     */
    public function canDriverDepart(int $driverId): bool
    {
        return !VehicleMovement::where('user_id', $driverId)
            ->where('status', 'out')
            ->exists();
    }

    /**
     * Check if a vehicle is available (not out)
     */
    public function isVehicleAvailable(int $vehicleId): bool
    {
        return !VehicleMovement::where('vehicle_id', $vehicleId)
            ->where('status', 'out')
            ->exists();
    }

    /**
     * Get driver status label
     */
    public function driverStatus(User $driver): string
    {
        $hasOpen = VehicleMovement::where('user_id', $driver->id)
            ->where('status', 'out')
            ->exists();
        if ($hasOpen) return 'لە دەرەوەیە';

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

            // Validate driver can depart
            if (!$override && !$this->canDriverDepart($driverId)) {
                throw new \Exception('ئەم شۆفێرە هێشتا نەگەڕاوەتەوە و ناتوانێت دەرچوونی نوێ تۆمار بکات.');
            }

            // Validate vehicle is available
            if (!$override && !$this->isVehicleAvailable($data['vehicle_id'])) {
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
                    'driver_id'  => $driverId,
                    'vehicle_id' => $data['vehicle_id'],
                    'destination'=> $data['destination'],
                    'override'   => $override,
                ],
            ]);

            // Notify admins
            $movement->loadMissing(['vehicle', 'driver']);
            $admins = User::where('role', 'admin')->where('active', true)->get();
            Notification::send($admins, new VehicleDeparted($movement));

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
                ],
            ]);

            // Notify admins
            $movement->loadMissing(['vehicle', 'driver']);
            $admins = User::where('role', 'admin')->where('active', true)->get();
            Notification::send($admins, new VehicleReturned($movement));

            return $movement;
        });
    }

    /**
     * Log audit for time modification
     */
    public function logTimeModification(VehicleMovement $movement, User $actor, array $changes): void
    {
        AuditLog::create([
            'user_id'     => $actor->id,
            'action'      => 'time_modified',
            'model_type'  => VehicleMovement::class,
            'model_id'    => $movement->id,
            'performed_at'=> now(),
            'details'     => $changes,
        ]);
    }
}
