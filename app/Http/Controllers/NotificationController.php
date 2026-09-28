<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = $user->notifications();

        if ($request->filter === 'unread') {
            $query = $user->unreadNotifications();
        } elseif ($request->filter === 'read') {
            $query = $user->readNotifications();
        }

        $notifications = $query->paginate(20)->withQueryString();

        return view('notifications.index', compact('notifications'));
    }

    public function read(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();

        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        // If notification has a movement_id, redirect to movement or dashboard
        $movementId = $notification?->data['movement_id'] ?? null;
        if ($movementId && $request->user()->isAdmin()) {
            return redirect()->route('movements.index');
        }

        return back();
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'هەموو ئاگادارکردنەوەکان وەک خوێندراوە دیاری کران.');
    }

    public function destroy(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->delete();
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'ئاگادارکردنەوە سڕایەوە.');
    }
}
