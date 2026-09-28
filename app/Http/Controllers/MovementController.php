<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMovement;
use App\Services\TripService;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    public function __construct(private TripService $tripService) {}

    /** Driver: show own trip history */
    public function mine()
    {
        $movements = VehicleMovement::with(['vehicle'])
            ->where('user_id', auth()->id())
            ->latest('departure_time')
            ->paginate(20);
        return view('driver.history', compact('movements'));
    }

    /** Driver: show active trip */
    public function active()
    {
        $movement = VehicleMovement::with(['vehicle'])
            ->where('user_id', auth()->id())
            ->where('status', 'out')
            ->first();
        return view('driver.active', compact('movement'));
    }

    /** Show departure form */
    public function create()
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        // Get all active vehicles
        $allVehicles = Vehicle::where('active', true)->orderBy('number')->get();

        // Get IDs of vehicles currently out
        $outVehicleIds = VehicleMovement::where('status', 'out')->pluck('vehicle_id')->toArray();

        // For drivers: only available vehicles. For admin: all with status indicator
        if ($isAdmin) {
            $vehicles = $allVehicles;
        } else {
            $vehicles = $allVehicles->filter(fn($v) => !in_array($v->id, $outVehicleIds));
        }

        // Get drivers list for admin
        $drivers = collect();
        if ($isAdmin) {
            $outDriverIds = VehicleMovement::where('status', 'out')->pluck('user_id')->toArray();
            $allDrivers = User::where('active', true)->orderBy('name')->get();
            $drivers = $allDrivers->map(function ($d) use ($outDriverIds) {
                $d->trip_status = in_array($d->id, $outDriverIds) ? 'لە دەرەوەیە' : 'بەردەست';
                return $d;
            });
        }

        // Check if current driver already has an open trip
        $hasOpenTrip = !$isAdmin && !$this->tripService->canDriverDepart($user->id);

        return view('driver.departure', compact('vehicles', 'drivers', 'isAdmin', 'hasOpenTrip', 'outVehicleIds'));
    }

    /** Store departure */
    public function store(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        $rules = [
            'vehicle_id'  => 'required|exists:vehicles,id',
            'destination' => 'required|string|max:255',
            'purpose'     => 'nullable|string|max:1000',
            'notes'       => 'nullable|string|max:1000',
        ];

        if ($isAdmin) {
            $rules['user_id'] = 'required|exists:users,id';
            $rules['override'] = 'nullable|boolean';
        }

        $data = $request->validate($rules);

        if (!$isAdmin) {
            $data['user_id'] = $user->id;
        }

        $override = $isAdmin && (
            $request->boolean('override') ||
            !$this->tripService->canDriverDepart($data['user_id']) ||
            !$this->tripService->isVehicleAvailable($data['vehicle_id'])
        );

        try {
            $this->tripService->recordDeparture($data, $user, $override);
            return redirect()->route('driver.active')->with('success', 'دەرچوون بە سەرکەوتوویی تۆمار کرا.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /** Record return */
    public function returnVehicle(VehicleMovement $movement)
    {
        $user = auth()->user();

        // Only the driver or admin can record return
        if ($movement->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        if ($movement->status !== 'out') {
            return back()->with('error', 'ئەم گەشتە پێشتر داخراوە.');
        }

        try {
            $this->tripService->recordReturn($movement, $user);
            return redirect()->route('driver.home')->with('success', 'گەڕانەوە بە سەرکەوتوویی تۆمار کرا.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /** Admin: view all movements */
    public function adminIndex()
    {
        $movements = VehicleMovement::with(['vehicle', 'driver'])
            ->latest('departure_time')
            ->paginate(30);

        $notReturned = VehicleMovement::with(['vehicle', 'driver'])
            ->where('status', 'out')
            ->oldest('departure_time')
            ->get();

        return view('admin.movements', compact('movements', 'notReturned'));
    }
}
