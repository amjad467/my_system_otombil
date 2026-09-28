<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $actions = [
            'user_login'           => 'چوونەژوورەوەی بەکارهێنەر',
            'user_logout'          => 'چوونەدەرەوەی بەکارهێنەر',
            'departure_created'    => 'تۆمارکردنی دەرچوون',
            'departure_overridden' => 'تۆمارکردنی دەرچوون بە تێپەڕاندن (Override)',
            'return_recorded'      => 'تۆمارکردنی گەڕانەوە',
            'user_created'         => 'زیادکردنی بەکارهێنەر',
            'user_updated'         => 'دەستکاری بەکارهێنەر',
            'user_deactivated'     => 'ناچالاککردنی بەکارهێنەر',
            'user_activated'       => 'چالاککردنەوەی بەکارهێنەر',
            'vehicle_created'      => 'زیادکردنی ئۆتۆمبێل',
            'vehicle_updated'      => 'دەستکاری ئۆتۆمبێل',
            'vehicle_deactivated'  => 'ناچالاککردنی ئۆتۆمبێل',
            'vehicle_activated'    => 'چالاککردنەوەی ئۆتۆمبێل',
            'backup_created'       => 'دروستکردنی باکئەپ',
            'backup_downloaded'    => 'داگرتنی باکئەپ',
            'backup_restored'      => 'گەڕاندنەوەی باکئەپ',
            'backup_deleted'       => 'سڕینەوەی باکئەپ',
            'settings_updated'     => 'نوێکردنەوەی ڕێکخستنەکان',
        ];

        $query = AuditLog::with('user')->latest('performed_at');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('performed_at', $request->date);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('action', 'like', "%{$s}%")
                  ->orWhere('details', 'like', "%{$s}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$s}%"));
            });
        }

        $logs = $query->paginate(25)->withQueryString();
        $users = User::orderBy('name')->get();

        return view('admin.audit-logs', compact('logs', 'actions', 'users'));
    }
}
