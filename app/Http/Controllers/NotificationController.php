<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function read(Request $r, string $id)
    {
        $notification = $r->user()->notifications()->where('id', $id)->first();

        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return redirect()->route('movements.index');
    }

    public function readAll(Request $r)
    {
        $r->user()->unreadNotifications->markAsRead();

        return back();
    }
}
