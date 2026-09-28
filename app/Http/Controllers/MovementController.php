<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMovement;
use App\Services\TripService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    public function __construct(private TripService $tripService)
    {
    }

    /** Driver: show own trip history */
    public function mine(Request $request)
    {
        $query = VehicleMovement::with(['vehicle'])
            ->where('user_id', auth()->id())
            ->latest('departure_time');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('destination', 'like', "%{$s}%")
                  ->orWhere('purpose', 'like', "%{$s}%")
                  ->orWhereHas('vehicle', fn($v) => $v->where('number', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $movements = $query->paginate(20)->withQueryString();

        if ($request->ajax()) {
            return view('driver.partials.history_rows', compact('movements'))->render();
        }

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
        $allVehicles = Vehicle::where('active', true)
            ->where('status', '!=', 'inactive')
            ->orderBy('number')
            ->get();

        // Get IDs of vehicles currently out
        $outVehicleIds = VehicleMovement::where('status', 'out')->pluck('vehicle_id')->toArray();

        // For regular drivers: only available (not out & not in maintenance)
        if ($isAdmin) {
            $vehicles = $allVehicles;
        } else {
            $vehicles = $allVehicles->filter(function ($v) use ($outVehicleIds) {
                return !in_array($v->id, $outVehicleIds) && $v->status !== 'maintenance';
            });
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

        $isDriverBusy = !$this->tripService->canDriverDepart($data['user_id']);
        $isVehicleUnavailable = !$this->tripService->isVehicleAvailable($data['vehicle_id']);

        $override = false;
        if ($isAdmin && ($request->boolean('override') || $isDriverBusy || $isVehicleUnavailable)) {
            $override = true;
        }

        if (!$isAdmin && ($isDriverBusy || $isVehicleUnavailable)) {
            $msg = $isDriverBusy ? 'شۆفێر لە دەرەوەیە و ناتوانێت گەشتی نوێ دەستپێبکات.' : 'ئەم ئۆتۆمبێلە بەردەست نییە.';
            return back()->withInput()->with('error', $msg);
        }

        try {
            $movement = $this->tripService->recordDeparture($data, $user, $override);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'دەرچوون بە سەرکەوتوویی تۆمار کرا.',
                    'redirect' => $isAdmin ? route('movements.index') : route('driver.active'),
                ]);
            }

            return redirect()->route($isAdmin ? 'movements.index' : 'driver.active')
                ->with('success', 'دەرچوون بە سەرکەوتوویی تۆمار کرا.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /** Record return */
    public function returnVehicle(Request $request, VehicleMovement $movement)
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

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'گەڕانەوە بە سەرکەوتوویی تۆمار کرا.',
                ]);
            }

            $redirectRoute = $user->isAdmin() ? 'movements.index' : 'driver.home';
            return redirect()->route($redirectRoute)->with('success', 'گەڕانەوە بە سەرکەوتوویی تۆمار کرا.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /** Admin: view all movements with filtering and live search */
    public function adminIndex(Request $request)
    {
        $query = VehicleMovement::with(['vehicle', 'driver', 'overrideUser'])
            ->latest('departure_time');

        // Search text
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('destination', 'like', "%{$s}%")
                  ->orWhere('purpose', 'like', "%{$s}%")
                  ->orWhereHas('driver', fn($d) => $d->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('vehicle', fn($v) => $v->where('number', 'like', "%{$s}%")->orWhere('type', 'like', "%{$s}%"));
            });
        }

        // Driver filter
        if ($request->filled('driver_id')) {
            $query->where('user_id', $request->driver_id);
        }

        // Vehicle filter
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('departure_time', $request->date);
        }

        $movements = $query->paginate(20)->withQueryString();

        // Not returned / Live list
        $notReturned = VehicleMovement::with(['vehicle', 'driver'])
            ->where('status', 'out')
            ->orderBy('departure_time', 'asc')
            ->get();

        $drivers = User::where('role', 'driver')->orderBy('name')->get();
        $vehicles = Vehicle::orderBy('number')->get();

        $attentionHours = (float) Setting::get('attention_hours', 2);
        $warningHours = (float) Setting::get('warning_hours', 4);

        if ($request->ajax()) {
            return view('admin.movements_table_partial', compact('movements', 'attentionHours', 'warningHours'))->render();
        }

        return view('admin.movements', compact('movements', 'notReturned', 'drivers', 'vehicles', 'attentionHours', 'warningHours'));
    }
}
