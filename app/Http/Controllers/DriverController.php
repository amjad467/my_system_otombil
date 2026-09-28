<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount([
            'movements as total_trips',
            'movements as active_trips' => fn($q) => $q->where('status', 'out'),
        ])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            if ($request->status === 'inactive') {
                $query->where('active', false);
            } elseif ($request->status === 'on_trip') {
                $query->where('active', true)->whereHas('movements', fn($q) => $q->where('status', 'out'));
            } elseif ($request->status === 'available') {
                $query->where('active', true)->whereDoesntHave('movements', fn($q) => $q->where('status', 'out'));
            }
        }

        $drivers = $query->paginate(15)->withQueryString();

        return view('admin.drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('admin.drivers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:50',
            'role'     => 'required|in:driver,admin',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create(array_merge($data, ['active' => true]));

        AuditLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'user_created',
            'model_type'   => User::class,
            'model_id'     => $user->id,
            'performed_at' => now(),
            'details'      => ['name' => $user->name, 'email' => $user->email, 'role' => $user->role],
        ]);

        return redirect()->route('drivers.index')->with('success', 'بەکارهێنەر بە سەرکەوتوویی زیادکرا.');
    }

    public function edit(User $driver)
    {
        return view('admin.drivers.edit', compact('driver'));
    }

    public function update(Request $request, User $driver)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|unique:users,email,' . $driver->id,
            'phone'    => 'nullable|string|max:50',
            'role'     => 'required|in:driver,admin',
            'password' => 'nullable|string|min:6|confirmed',
            'active'   => 'nullable|boolean',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $data['active'] = $request->has('active') ? (bool)$request->active : false;

        $driver->update($data);

        AuditLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'user_updated',
            'model_type'   => User::class,
            'model_id'     => $driver->id,
            'performed_at' => now(),
            'details'      => ['name' => $driver->name, 'email' => $driver->email, 'role' => $driver->role, 'active' => $driver->active],
        ]);

        return redirect()->route('drivers.index')->with('success', 'زانیاری بەکارهێنەر بە سەرکەوتوویی نوێکرایەوە.');
    }

    public function destroy(User $driver)
    {
        if ($driver->id === auth()->id()) {
            return back()->with('error', 'ناتوانیت هەژماری خۆت ناچالاک بکەیت.');
        }

        if ($driver->movements()->where('status', 'out')->exists()) {
            return back()->with('error', 'ئەم بەکارهێنەرە گەشتێکی کراوەی هەیە و ناتوانرێت ناچالاک بکرێت.');
        }

        $newStatus = !$driver->active;
        $driver->update(['active' => $newStatus]);

        AuditLog::create([
            'user_id'      => auth()->id(),
            'action'       => $newStatus ? 'user_activated' : 'user_deactivated',
            'model_type'   => User::class,
            'model_id'     => $driver->id,
            'performed_at' => now(),
            'details'      => ['name' => $driver->name, 'active' => $newStatus],
        ]);

        $msg = $newStatus ? 'بەکارهێنەر چالاک کرایەوە.' : 'بەکارهێنەر ناچالاک کرا.';
        return back()->with('success', $msg);
    }
}
