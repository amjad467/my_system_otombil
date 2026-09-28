<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$query, $filters] = $this->buildQuery($request);

        $movements = $query->paginate(30)->withQueryString();

        $drivers = User::where('role', 'driver')->orderBy('name')->get();
        $vehicles = Vehicle::orderBy('number')->get();

        // Summary metrics for the filtered result
        $metricsQuery = (clone $query)->getQuery();
        $totalTrips = (clone $query)->count();
        $completedTrips = (clone $query)->where('status', 'returned')->count();
        $totalMinutes = (clone $query)->where('status', 'returned')->sum('duration_minutes');
        $totalHours = round($totalMinutes / 60, 1);

        $summary = [
            'total_trips' => $totalTrips,
            'completed'   => $completedTrips,
            'total_hours' => $totalHours,
            'avg_minutes' => $completedTrips ? round($totalMinutes / $completedTrips) : 0,
        ];

        return view('reports.index', compact('movements', 'drivers', 'vehicles', 'filters', 'summary'));
    }

    private function buildQuery(Request $request)
    {
        $filters = $request->validate([
            'preset'      => 'nullable|in:today,week,month,year',
            'from'        => 'nullable|date',
            'to'          => 'nullable|date',
            'driver_id'   => 'nullable|integer',
            'vehicle_id'  => 'nullable|integer',
            'status'      => 'nullable|in:out,returned',
            'destination' => 'nullable|string|max:255',
        ]);

        // Preset handling
        if (!empty($filters['preset'])) {
            $now = Carbon::now();
            switch ($filters['preset']) {
                case 'today':
                    $filters['from'] = $now->toDateString();
                    $filters['to'] = $now->toDateString();
                    break;
                case 'week':
                    $filters['from'] = $now->copy()->startOfWeek()->toDateString();
                    $filters['to'] = $now->copy()->endOfWeek()->toDateString();
                    break;
                case 'month':
                    $filters['from'] = $now->copy()->startOfMonth()->toDateString();
                    $filters['to'] = $now->copy()->endOfMonth()->toDateString();
                    break;
                case 'year':
                    $filters['from'] = $now->copy()->startOfYear()->toDateString();
                    $filters['to'] = $now->copy()->endOfYear()->toDateString();
                    break;
            }
        }

        $query = VehicleMovement::with(['vehicle', 'driver'])
            ->when($filters['from'] ?? null, fn($q, $v) => $q->whereDate('departure_time', '>=', $v))
            ->when($filters['to'] ?? null, fn($q, $v) => $q->whereDate('departure_time', '<=', $v))
            ->when($filters['driver_id'] ?? null, fn($q, $v) => $q->where('user_id', $v))
            ->when($filters['vehicle_id'] ?? null, fn($q, $v) => $q->where('vehicle_id', $v))
            ->when($filters['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->when($filters['destination'] ?? null, fn($q, $v) => $q->where('destination', 'like', "%{$v}%"))
            ->latest('departure_time');

        return [$query, $filters];
    }

    public function csv(Request $request): StreamedResponse
    {
        [$query] = $this->buildQuery($request);

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            // Write UTF-8 BOM for Arabic/Kurdish characters in Excel
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'شۆفێر',
                'ژمارەی ئۆتۆمبێل',
                'جۆری ئۆتۆمبێل',
                'مەبەست',
                'هۆکار',
                'کاتی دەرچوون',
                'کاتی گەڕانەوە',
                'ماوە',
                'دۆخ',
                'تێبینی'
            ]);

            $query->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $m) {
                    $durationStr = '';
                    if ($m->duration_minutes !== null) {
                        $durationStr = floor($m->duration_minutes / 60) . ' کاتژمێر ' . ($m->duration_minutes % 60) . ' خولەک';
                    } elseif ($m->status === 'out') {
                        $live = Carbon::now()->diffInMinutes($m->departure_time);
                        $durationStr = floor($live / 60) . ' کاتژمێر ' . ($live % 60) . ' خولەک (کراوە)';
                    }

                    fputcsv($out, [
                        $m->driver?->name ?? '—',
                        $m->vehicle?->number ?? '—',
                        $m->vehicle?->type ?? '—',
                        $m->destination,
                        $m->purpose ?? '',
                        $m->departure_time?->format('Y-m-d H:i') ?? '',
                        $m->return_time?->format('Y-m-d H:i') ?? '—',
                        $durationStr,
                        $m->status === 'out' ? 'لە دەرەوە' : 'گەڕاوەتەوە',
                        $m->notes ?? ''
                    ]);
                }
            });

            fclose($out);
        }, 'vehicle-movements-report-' . now()->format('Y-m-d_H-i') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="vehicle-report.csv"',
        ]);
    }
}
