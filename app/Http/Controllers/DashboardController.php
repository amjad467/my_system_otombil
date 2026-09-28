<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMovement;
use App\Services\TripService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(private TripService $tripService)
    {
    }

    public function index()
    {
        $today = Carbon::today();
        $user = auth()->user();

        // Trigger overdue check quietly
        try {
            $this->tripService->checkOverdueTrips();
        } catch (\Throwable $e) {
            // Ignore background check failure
        }

        $attentionHours = (float) Setting::get('attention_hours', 2);
        $warningHours = (float) Setting::get('warning_hours', 4);

        // Core counts
        $totalVehicles = Vehicle::where('active', true)->count();
        $totalDrivers = User::where('role', 'driver')->where('active', true)->count();
        $todayTrips = VehicleMovement::whereDate('departure_time', $today)->count();
        $currentlyOut = VehicleMovement::where('status', 'out')->count();
        $returnedToday = VehicleMovement::where('status', 'returned')
            ->whereDate('return_time', $today)
            ->count();

        // Calculate overdue movements
        $warningCutoff = Carbon::now()->subHours($warningHours);
        $overdueCount = VehicleMovement::where('status', 'out')
            ->where('departure_time', '<=', $warningCutoff)
            ->count();

        $stats = [
            'vehicles'       => $totalVehicles,
            'drivers'        => $totalDrivers,
            'today'          => $todayTrips,
            'out'            => $currentlyOut,
            'returned_today' => $returnedToday,
            'overdue'        => $overdueCount,
            'available'      => Vehicle::where('active', true)
                ->where('status', '!=', 'maintenance')
                ->whereDoesntHave('movements', fn($q) => $q->where('status', 'out'))
                ->count(),
            'maintenance'    => Vehicle::where('status', 'maintenance')->count(),
        ];

        // Live Trips currently outside
        $liveTrips = VehicleMovement::with(['vehicle', 'driver'])
            ->where('status', 'out')
            ->orderBy('departure_time', 'asc')
            ->get();

        // Recent completed or active movements
        $recent = VehicleMovement::with(['vehicle', 'driver'])
            ->latest('departure_time')
            ->limit(8)
            ->get();

        // Driver-specific active trip if user is a driver
        $myActiveTrip = null;
        if ($user->isDriver()) {
            $myActiveTrip = VehicleMovement::with(['vehicle'])
                ->where('user_id', $user->id)
                ->where('status', 'out')
                ->first();
        }

        // Statistics for charts (Admin & General dashboard)
        // 1. Trips last 7 days
        $daysLabels = [];
        $daysData = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $daysLabels[] = $d->locale('ku')->isoFormat('ddd D/M');
            $daysData[] = VehicleMovement::whereDate('departure_time', $d)->count();
        }

        // 2. Top drivers by trip count
        $topDrivers = User::where('role', 'driver')
            ->withCount('movements')
            ->orderByDesc('movements_count')
            ->limit(5)
            ->get();

        // 3. Most used vehicles
        $topVehicles = Vehicle::withCount('movements')
            ->orderByDesc('movements_count')
            ->limit(5)
            ->get();

        // 4. Overall trip duration statistics
        $allCompletedMovements = VehicleMovement::where('status', 'returned')
            ->whereNotNull('duration_minutes');

        $totalMinutes = (clone $allCompletedMovements)->sum('duration_minutes');
        $avgMinutes = (clone $allCompletedMovements)->avg('duration_minutes') ?: 0;

        $stats['total_hours'] = round($totalMinutes / 60, 1);
        $stats['avg_hours'] = round($avgMinutes / 60, 1);
        $stats['avg_mins'] = round($avgMinutes % 60);

        return view('dashboard', compact(
            'stats',
            'liveTrips',
            'recent',
            'myActiveTrip',
            'daysLabels',
            'daysData',
            'topDrivers',
            'topVehicles',
            'attentionHours',
            'warningHours'
        ));
    }
}
