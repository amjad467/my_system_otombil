<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMovement;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q = trim($request->input('q', ''));
        $user = auth()->user();

        if (mb_strlen($q) < 2) {
            if ($request->ajax()) {
                return response()->json([
                    'drivers'       => [],
                    'vehicles'      => [],
                    'movements'     => [],
                    'notifications' => [],
                ]);
            }
            return view('search.index', [
                'query'         => $q,
                'drivers'       => collect(),
                'vehicles'      => collect(),
                'movements'     => collect(),
                'notifications' => collect(),
            ]);
        }

        // Drivers (Admins can see all, drivers see their colleagues)
        $drivers = User::where('name', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->orWhere('email', 'like', "%{$q}%")
            ->limit(5)
            ->get();

        // Vehicles
        $vehicles = Vehicle::where('number', 'like', "%{$q}%")
            ->orWhere('type', 'like', "%{$q}%")
            ->orWhere('model', 'like', "%{$q}%")
            ->limit(5)
            ->get();

        // Trips / Movements
        $movementsQuery = VehicleMovement::with(['vehicle', 'driver'])
            ->where(function ($query) use ($q) {
                $query->where('destination', 'like', "%{$q}%")
                      ->orWhere('purpose', 'like', "%{$q}%")
                      ->orWhereHas('driver', fn($d) => $d->where('name', 'like', "%{$q}%"))
                      ->orWhereHas('vehicle', fn($v) => $v->where('number', 'like', "%{$q}%"));
            });

        if (!$user->isAdmin()) {
            $movementsQuery->where('user_id', $user->id);
        }

        $movements = $movementsQuery->latest('departure_time')->limit(6)->get();

        // Notifications
        $notifications = $user->notifications()
            ->where('data', 'like', "%{$q}%")
            ->limit(5)
            ->get();

        if ($request->ajax()) {
            return response()->json([
                'drivers'       => $drivers->map(fn($d) => [
                    'id'     => $d->id,
                    'name'   => $d->name,
                    'role'   => $d->role === 'admin' ? 'بەڕێوەبەر' : 'شۆفێر',
                    'status' => $d->status_label,
                    'url'    => $user->isAdmin() ? route('drivers.edit', $d) : '#',
                ]),
                'vehicles'      => $vehicles->map(fn($v) => [
                    'id'     => $v->id,
                    'number' => $v->number,
                    'type'   => $v->type,
                    'status' => $v->status_label,
                    'url'    => $user->isAdmin() ? route('vehicles.edit', $v) : '#',
                ]),
                'movements'     => $movements->map(fn($m) => [
                    'id'             => $m->id,
                    'driver'         => $m->driver?->name,
                    'vehicle'        => $m->vehicle?->number,
                    'destination'    => $m->destination,
                    'departure_time' => $m->departure_time?->format('Y-m-d H:i'),
                    'status'         => $m->status === 'out' ? 'لە دەرەوەیە' : 'گەڕاوەتەوە',
                    'url'            => $user->isAdmin() ? route('movements.index') . '?search=' . urlencode($m->destination) : route('driver.history'),
                ]),
                'notifications' => $notifications->map(fn($n) => [
                    'id'    => $n->id,
                    'title' => $n->data['title'] ?? 'ئاگاداری',
                    'body'  => $n->data['body'] ?? '',
                    'time'  => $n->created_at->diffForHumans(),
                    'url'   => route('notifications.index'),
                ]),
            ]);
        }

        return view('search.index', compact('q', 'drivers', 'vehicles', 'movements', 'notifications'));
    }
}
