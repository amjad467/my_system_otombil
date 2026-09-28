<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\VehicleMaintenanceAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::with(['activeMovement.driver'])
            ->withCount('movements')
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('number', 'like', "%{$s}%")
                  ->orWhere('type', 'like', "%{$s}%")
                  ->orWhere('model', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'inactive') {
                $query->where(fn($q) => $q->where('active', false)->orWhere('status', 'inactive'));
            } elseif ($request->status === 'maintenance') {
                $query->where('status', 'maintenance');
            } elseif ($request->status === 'on_trip') {
                $query->whereHas('movements', fn($q) => $q->where('status', 'out'));
            } elseif ($request->status === 'available') {
                $query->where('active', true)
                      ->where('status', 'available')
                      ->whereDoesntHave('movements', fn($q) => $q->where('status', 'out'));
            }
        }

        $vehicles = $query->paginate(15)->withQueryString();

        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('admin.vehicles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'number'      => 'required|string|max:80|unique:vehicles,number',
            'type'        => 'required|string|max:100',
            'model'       => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'status'      => 'nullable|in:available,maintenance,inactive',
        ]);

        $vehicle = Vehicle::create(array_merge($data, [
            'active' => ($data['status'] ?? 'available') !== 'inactive',
            'status' => $data['status'] ?? 'available',
        ]));

        AuditLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'vehicle_created',
            'model_type'   => Vehicle::class,
            'model_id'     => $vehicle->id,
            'performed_at' => now(),
            'details'      => ['number' => $vehicle->number, 'type' => $vehicle->type],
        ]);

        return redirect()->route('vehicles.index')->with('success', 'ئۆتۆمبێل بە سەرکەوتوویی زیادکرا.');
    }

    public function edit(Vehicle $vehicle)
    {
        return view('admin.vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'number'      => 'required|string|max:80|unique:vehicles,number,' . $vehicle->id,
            'type'        => 'required|string|max:100',
            'model'       => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'status'      => 'required|in:available,maintenance,inactive',
            'active'      => 'nullable|boolean',
        ]);

        $previousStatus = $vehicle->status;
        $data['active'] = $data['status'] !== 'inactive';

        $vehicle->update($data);

        // If status changed to maintenance, alert admins
        if ($data['status'] === 'maintenance' && $previousStatus !== 'maintenance') {
            $admins = User::where('role', 'admin')->where('active', true)->get();
            Notification::send($admins, new VehicleMaintenanceAlert($vehicle, $data['description'] ?? 'پشکنینی ئاسایی'));
        }

        AuditLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'vehicle_updated',
            'model_type'   => Vehicle::class,
            'model_id'     => $vehicle->id,
            'performed_at' => now(),
            'details'      => ['number' => $vehicle->number, 'status' => $vehicle->status],
        ]);

        return redirect()->route('vehicles.index')->with('success', 'زانیاری ئۆتۆمبێل نوێکرایەوە.');
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->movements()->where('status', 'out')->exists()) {
            return back()->with('error', 'ئەم ئۆتۆمبێلە ئێستا لە دەرەوەیە و ناتوانرێت ناچالاک بکرێت.');
        }

        $newActive = !$vehicle->active;
        $newStatus = $newActive ? 'available' : 'inactive';
        $vehicle->update(['active' => $newActive, 'status' => $newStatus]);

        AuditLog::create([
            'user_id'      => auth()->id(),
            'action'       => $newActive ? 'vehicle_activated' : 'vehicle_deactivated',
            'model_type'   => Vehicle::class,
            'model_id'     => $vehicle->id,
            'performed_at' => now(),
            'details'      => ['number' => $vehicle->number, 'active' => $newActive],
        ]);

        $msg = $newActive ? 'ئۆتۆمبێل چالاک کرایەوە.' : 'ئۆتۆمبێل ناچالاک کرا.';
        return back()->with('success', $msg);
    }
}
