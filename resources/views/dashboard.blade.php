@extends('layouts.app') @section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>داشبۆرد</h2>
        <p class="text-muted mb-0">پوختەی دۆخی سیستەم و جوڵەکانی ئۆتۆمبێل</p>
    </div>
    @if(auth()->user()->isAdmin())
    <div class="d-flex gap-2">
        <a href="{{ route('movements.index') }}" class="btn btn-outline-primary">هەموو جوڵەکان</a>
        <a href="{{ route('vehicles.index') }}" class="btn btn-outline-primary">ئۆتۆمبێلەکان</a>
        <a href="{{ route('reports.index') }}" class="btn btn-primary">ڕاپۆرتەکان</a>
    </div>
    @else
    <a href="{{ route('driver.departure') }}" class="btn btn-primary mobile-action">➕ تۆمارکردنی دەرچوون</a>
    @endif
</div>

{{-- ئامارەکان --}}
<div class="row g-3 mb-4">
    @php
    $cards = [
        ['کۆی ئۆتۆمبێلەکان', $stats['vehicles'], 'primary', '🚗'],
        ['بەردەست', $stats['available'], 'success', '✅'],
        ['لە دەرەوە', $stats['out'], 'warning', '🔶'],
        ['گەشتەکانی ئەمڕۆ', $stats['today'], 'info', '📊'],
        ['شۆفێرە چالاکەکان', $stats['drivers'], 'secondary', '👤'],
    ];
    @endphp
    @foreach($cards as $s)
    <div class="col-6 col-lg">
        <div class="card p-3">
            <div class="text-muted"><span class="me-1">{{ $s[3] }}</span>{{ $s[0] }}</div>
            <div class="stat text-{{ $s[2] }}">{{ $s[1] }}</div>
        </div>
    </div>
    @endforeach
</div>

@if(auth()->user()->isAdmin())
{{-- ئامارەکانی زیاتر بۆ ئەدمین --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted">👤 شۆفێران لە دەرەوە</div>
            <div class="stat text-warning">{{ $stats['drivers_out'] ?? 0 }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted">🚗 ئۆتۆمبێلان لە دەرەوە</div>
            <div class="stat text-warning">{{ $stats['cars_out'] ?? 0 }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted">✅ گەڕاوەکانی ئەمڕۆ</div>
            <div class="stat text-success">{{ $stats['today_returns'] ?? 0 }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted">⏱ کۆی ماوەی گەشتەکان</div>
            <div class="stat text-info" style="font-size:22px;">{{ $stats['total_duration_hours'] ?? 0 }} کاتژمێر {{ $stats['total_duration_mins'] ?? 0 }} خولەک</div>
        </div>
    </div>
</div>

{{-- لیستی نەگەڕاوەکان --}}
@if(isset($notReturned) && $notReturned->count() > 0)
<div class="card mb-4 border-warning">
    <div class="card-body">
        <h5 class="text-warning-emphasis mb-3">🚧 ئۆتۆمبێلە نەگەڕاوەکان ({{ $notReturned->count() }})</h5>
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead>
                    <tr><th>شۆفێر</th><th>ژمارەی ئۆتۆمبێل</th><th>جۆری ئۆتۆمبێل</th><th>مەبەست</th><th>کاتی دەرچوون</th><th>ماوەی ئێستا</th></tr>
                </thead>
                <tbody>
                    @foreach($notReturned as $m)
                    <tr>
                        <td>{{ $m->driver->name }}</td>
                        <td class="fw-bold">{{ $m->vehicle->number }}</td>
                        <td>{{ $m->vehicle->type }}</td>
                        <td>{{ $m->destination }}</td>
                        <td>{{ $m->departure_time->format('Y-m-d H:i') }}</td>
                        <td>
                            <span class="badge bg-warning text-dark">
                                {{ intdiv($m->current_duration, 60) }} کاتژمێر {{ $m->current_duration % 60 }} خولەک
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endif

{{-- دوا جوڵەکان --}}
<div class="card">
    <div class="card-body">
        <h5>دوا جوڵەکان</h5>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr><th>شۆفێر</th><th>ئۆتۆمبێل</th><th>مەبەست</th><th>دەرچوون</th><th>گەڕانەوە</th><th>ماوە</th><th>دۆخ</th></tr>
                </thead>
                <tbody>
                    @forelse($recent as $m)
                    <tr>
                        <td>{{ $m->driver->name }}</td>
                        <td>{{ $m->vehicle->number }}</td>
                        <td>{{ $m->destination }}</td>
                        <td>{{ $m->departure_time->format('Y-m-d H:i') }}</td>
                        <td>{{ $m->return_time?->format('Y-m-d H:i') ?? '—' }}</td>
                        <td>
                            @if($m->duration_minutes !== null)
                                {{ intdiv($m->duration_minutes, 60) }} کاتژمێر {{ $m->duration_minutes % 60 }} خولەک
                            @elseif($m->status === 'out')
                                @php $live = \Carbon\Carbon::now()->diffInMinutes($m->departure_time); @endphp
                                <span class="text-warning">{{ intdiv($live, 60) }}:{{ str_pad($live % 60, 2, '0', STR_PAD_LEFT) }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $m->status==='out' ? 'badge-out' : 'badge-returned' }}">
                                {{ $m->status==='out' ? 'لە دەرەوە' : 'گەڕاوەتەوە' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center">هیچ جوڵەیەک نییە.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
