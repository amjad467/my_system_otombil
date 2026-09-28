@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">داشبۆردی بەڕێوەبردن</h2>
        <p class="text-muted mb-0 small">چاودێری ڕاستەوخۆی دۆخی ئۆتۆمبێلەکان، شۆفێران و دەرچوون و گەڕانەوەکان</p>
    </div>

    <!-- Quick Actions -->
    <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
        <a href="{{ route('driver.departure') }}" class="btn btn-primary d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0 justify-content-center shadow-sm">
            <i class="bi bi-plus-circle-fill"></i>
            <span>دەرچوونی نوێ</span>
        </a>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('vehicles.create') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0 justify-content-center">
                <i class="bi bi-car-front"></i>
                <span class="d-none d-sm-inline">زیادکردنی</span> ئۆتۆمبێل
            </a>
            <a href="{{ route('drivers.create') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0 justify-content-center">
                <i class="bi bi-person-plus"></i>
                <span class="d-none d-sm-inline">زیادکردنی</span> شۆفێر
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-primary d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0 justify-content-center">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>ڕاپۆرت</span>
            </a>
        @endif
    </div>
</div>

<!-- Driver Active Trip Alert Card (if current user is a driver with an active trip) -->
@if(isset($myActiveTrip) && $myActiveTrip)
<div class="card border-primary border-2 mb-4 bg-primary bg-opacity-10 shadow-sm">
    <div class="card-body p-4 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fs-3 flex-shrink-0" style="width: 54px; height: 54px;">
                🚗
            </div>
            <div>
                <span class="badge bg-primary mb-1">گەشتی کراوەت هەیە</span>
                <h5 class="fw-bold mb-1">{{ $myActiveTrip->vehicle?->number }} ({{ $myActiveTrip->vehicle?->type }}) — بەرەو {{ $myActiveTrip->destination }}</h5>
                <div class="text-muted small">
                    دەرچوون: {{ $myActiveTrip->departure_time->format('Y-m-d H:i') }} |
                    ماوەی ئێستا: <span class="fw-bold text-primary live-timer" data-start="{{ $myActiveTrip->departure_time->toISOString() }}">...</span>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('driver.return', $myActiveTrip) }}" class="m-0 w-100 w-md-auto" onsubmit="return confirm('ئایا دڵنیایت لە تۆمارکردنی گەڕانەوەی ئەم ئۆتۆمبێلە؟')">
            @csrf
            <button type="submit" class="btn btn-success btn-lg w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-check2-circle fs-5"></i>
                <span>تۆمارکردنی گەڕانەوە</span>
            </button>
        </form>
    </div>
</div>
@endif

<!-- Top Overview Statistics Cards -->
<div class="row g-3 mb-4">
    <!-- Total Drivers -->
    <div class="col-6 col-lg-2">
        <div class="card h-100 p-3 border-0 shadow-sm position-relative overflow-hidden">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">کۆی شۆفێران</span>
                <span class="p-2 rounded-3 bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-people-fill"></i></span>
            </div>
            <div class="fs-3 fw-bold text-body">{{ $stats['drivers'] }}</div>
            <small class="text-muted" style="font-size: 11px;">شۆفێری چالاک</small>
        </div>
    </div>

    <!-- Total Vehicles -->
    <div class="col-6 col-lg-2">
        <div class="card h-100 p-3 border-0 shadow-sm position-relative overflow-hidden">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">کۆی ئۆتۆمبێلەکان</span>
                <span class="p-2 rounded-3 bg-primary bg-opacity-10 text-primary"><i class="bi bi-car-front-fill"></i></span>
            </div>
            <div class="fs-3 fw-bold text-primary">{{ $stats['vehicles'] }}</div>
            <small class="text-success" style="font-size: 11px;">{{ $stats['available'] }} بەردەست</small>
        </div>
    </div>

    <!-- Today's Trips -->
    <div class="col-6 col-lg-2">
        <div class="card h-100 p-3 border-0 shadow-sm position-relative overflow-hidden">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">گەشتەکانی ئەمڕۆ</span>
                <span class="p-2 rounded-3 bg-info bg-opacity-10 text-info"><i class="bi bi-calendar2-check-fill"></i></span>
            </div>
            <div class="fs-3 fw-bold text-info">{{ $stats['today'] }}</div>
            <small class="text-muted" style="font-size: 11px;">تۆمارکراوی ئەمڕۆ</small>
        </div>
    </div>

    <!-- Currently Outside -->
    <div class="col-6 col-lg-2">
        <div class="card h-100 p-3 border-0 shadow-sm position-relative overflow-hidden">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">ئێستا لە دەرەوەن</span>
                <span class="p-2 rounded-3 bg-warning bg-opacity-10 text-warning"><i class="bi bi-send-fill"></i></span>
            </div>
            <div class="fs-3 fw-bold text-warning">{{ $stats['out'] }}</div>
            <small class="text-muted" style="font-size: 11px;">گەشتی کراوە</small>
        </div>
    </div>

    <!-- Returned Today -->
    <div class="col-6 col-lg-2">
        <div class="card h-100 p-3 border-0 shadow-sm position-relative overflow-hidden">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">گەڕاوەکانی ئەمڕۆ</span>
                <span class="p-2 rounded-3 bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle-fill"></i></span>
            </div>
            <div class="fs-3 fw-bold text-success">{{ $stats['returned_today'] }}</div>
            <small class="text-muted" style="font-size: 11px;">گەڕانەوەی تەواو</small>
        </div>
    </div>

    <!-- Overdue / Not Returned -->
    <div class="col-6 col-lg-2">
        <div class="card h-100 p-3 border-0 shadow-sm position-relative overflow-hidden {{ $stats['overdue'] > 0 ? 'border border-danger border-2' : '' }}">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">دواکەوتووەکان</span>
                <span class="p-2 rounded-3 bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-triangle-fill"></i></span>
            </div>
            <div class="fs-3 fw-bold text-danger">{{ $stats['overdue'] }}</div>
            <small class="text-danger" style="font-size: 11px;">زیاتر لە {{ $warningHours }} کاتژمێر</small>
        </div>
    </div>
</div>

<!-- Section: Live Trips (ئۆتۆمبێلەکان لە دەرەوە) -->
<div class="card mb-4 border-0 shadow-sm overflow-hidden">
    <div class="card-header bg-transparent border-bottom p-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <span class="p-2 rounded-circle bg-warning bg-opacity-10 text-warning"><i class="bi bi-broadcast fs-5"></i></span>
            <div>
                <h5 class="fw-bold mb-0">ئۆتۆمبێلە لە دەرەوەکان (Live Trips)</h5>
                <small class="text-muted">چاودێری ڕاستەوخۆی ئەو گەشتانەی هێشتا نەگەڕاونەتەوە</small>
            </div>
        </div>
        <span class="badge bg-warning text-dark fs-6">{{ $liveTrips->count() }} لە دەرەوەیە</span>
    </div>

    @if($liveTrips->isEmpty())
        <div class="p-5 text-center text-muted">
            <i class="bi bi-shield-check fs-1 text-success d-block mb-2"></i>
            <h5 class="fw-bold">هەموو ئۆتۆمبێلەکان گەڕاونەتەوە!</h5>
            <p class="small text-muted mb-0">لە ئێستادا هیچ ئۆتۆمبێلێک لە دەرەوە نییە.</p>
        </div>
    @else
        <!-- Desktop Table View -->
        <div class="responsive-table-desktop">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>شۆفێر</th>
                        <th>ئۆتۆمبێل</th>
                        <th>مەبەست</th>
                        <th>کاتی دەرچوون</th>
                        <th>ماوەی ئێستا (Live)</th>
                        <th>دۆخی دواکەوتن</th>
                        <th class="text-end">کردار</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($liveTrips as $trip)
                        @php
                            $minutes = $trip->current_duration_minutes;
                            $overdue = $trip->overdue_status;
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-secondary bg-opacity-10 text-body d-flex align-items-center justify-content-center fw-bold" style="width: 34px; height: 34px;">
                                        {{ mb_substr($trip->driver?->name ?? '—', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $trip->driver?->name ?? 'نادیار' }}</div>
                                        <small class="text-muted">{{ $trip->driver?->phone ?: '—' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">{{ $trip->vehicle?->number }}</div>
                                <small class="text-muted">{{ $trip->vehicle?->type }} {{ $trip->vehicle?->model ? '('.$trip->vehicle?->model.')' : '' }}</small>
                            </td>
                            <td>
                                <div>{{ $trip->destination }}</div>
                                @if($trip->purpose)
                                    <small class="text-muted">{{ Str::limit($trip->purpose, 30) }}</small>
                                @endif
                            </td>
                            <td>
                                <div>{{ $trip->departure_time->format('H:i') }}</div>
                                <small class="text-muted">{{ $trip->departure_time->format('Y-m-d') }}</small>
                            </td>
                            <td>
                                <span class="fw-bold live-timer" data-start="{{ $trip->departure_time->toISOString() }}">
                                    {{ intdiv($minutes, 60) }} کاتژمێر {{ $minutes % 60 }} خولەک
                                </span>
                            </td>
                            <td>
                                @if($overdue === 'warning')
                                    <span class="badge-danger-glow">⚠️ مەترسی ({{ intdiv($minutes, 60) }}+ کاتژمێر)</span>
                                @elseif($overdue === 'attention')
                                    <span class="badge-warning-glow">⚠️ سەرنج پێویستە</span>
                                @else
                                    <span class="badge-normal">✅ ئاسایی</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if(auth()->user()->isAdmin() || auth()->id() === $trip->user_id)
                                    <form method="POST" action="{{ route('driver.return', $trip) }}" class="d-inline" onsubmit="return confirm('ئایا دڵنیایت لە گەڕاندنەوەی ئەم ئۆتۆمبێلە؟')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="bi bi-check2"></i> گەڕانەوە
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="p-3 d-lg-none">
            <div class="row g-3">
                @foreach($liveTrips as $trip)
                    @php
                        $minutes = $trip->current_duration_minutes;
                        $overdue = $trip->overdue_status;
                    @endphp
                    <div class="col-12 mobile-card-item">
                        <div class="card p-3 border shadow-none bg-body-tertiary">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-bold mb-0 text-primary">{{ $trip->vehicle?->number }} <span class="text-muted small">({{ $trip->vehicle?->type }})</span></h6>
                                    <div class="small text-muted"><i class="bi bi-person me-1"></i>{{ $trip->driver?->name }}</div>
                                </div>
                                @if($overdue === 'warning')
                                    <span class="badge-danger-glow" style="font-size: 11px;">⚠️ مەترسی</span>
                                @elseif($overdue === 'attention')
                                    <span class="badge-warning-glow" style="font-size: 11px;">⚠️ سەرنج</span>
                                @else
                                    <span class="badge-normal" style="font-size: 11px;">ئاسایی</span>
                                @endif
                            </div>

                            <div class="bg-body p-2 rounded-3 mb-2 small">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">مەبەست:</span>
                                    <span class="fw-semibold">{{ $trip->destination }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">کاتی دەرچوون:</span>
                                    <span>{{ $trip->departure_time->format('Y-m-d H:i') }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">ماوەی لە دەرەوە:</span>
                                    <span class="fw-bold text-primary live-timer" data-start="{{ $trip->departure_time->toISOString() }}">
                                        {{ intdiv($minutes, 60) }} کاتژمێر {{ $minutes % 60 }} خولەک
                                    </span>
                                </div>
                            </div>

                            @if(auth()->user()->isAdmin() || auth()->id() === $trip->user_id)
                                <form method="POST" action="{{ route('driver.return', $trip) }}" onsubmit="return confirm('تۆمارکردنی گەڕانەوە بۆ {{ $trip->vehicle?->number }}؟')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success w-100">
                                        <i class="bi bi-check-circle me-1"></i> تۆمارکردنی گەڕانەوە
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Section: Statistics & Charts (Chart.js) -->
<div class="row g-4 mb-4">
    <!-- Chart: Trips Last 7 Days -->
    <div class="col-12 col-lg-8">
        <div class="card h-100 border-0 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0">ئاماری گەشتەکانی ٧ ڕۆژی ڕابردوو</h5>
                    <small class="text-muted">ژمارەی دەرچوونەکانی ئۆتۆمبێل بە پێی ڕۆژ</small>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary p-2"><i class="bi bi-graph-up"></i></span>
            </div>
            <div style="position: relative; height: 260px;">
                <canvas id="tripsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="col-12 col-lg-4">
        <div class="card h-100 border-0 shadow-sm p-4 d-flex flex-column justify-content-between">
            <h5 class="fw-bold mb-3">پوختەی کات و جوڵە</h5>

            <div class="p-3 rounded-4 bg-body-tertiary mb-3">
                <div class="text-muted small mb-1"><i class="bi bi-clock-history me-1 text-primary"></i> کۆی کاتی هەموو گەشتەکان</div>
                <div class="fs-4 fw-bold text-primary">{{ $stats['total_hours'] }} کاتژمێر</div>
                <small class="text-muted">بۆ گەشتە تەواوبووەکان</small>
            </div>

            <div class="p-3 rounded-4 bg-body-tertiary mb-3">
                <div class="text-muted small mb-1"><i class="bi bi-hourglass-split me-1 text-info"></i> تێکڕای ماوەی هەر گەشتێک</div>
                <div class="fs-4 fw-bold text-info">{{ $stats['avg_hours'] }} کاتژمێر <span class="fs-6 text-muted">({{ $stats['avg_mins'] }} خولەک)</span></div>
                <small class="text-muted">ناوەندی گشتی</small>
            </div>

            <div class="p-3 rounded-4 bg-body-tertiary">
                <div class="text-muted small mb-1"><i class="bi bi-wrench-adjustable me-1 text-danger"></i> ئۆتۆمبێل لە چاککردنەوەدا</div>
                <div class="fs-4 fw-bold text-danger">{{ $stats['maintenance'] }} ئۆتۆمبێل</div>
                <small class="text-muted">پێویستی بە سەرنج هەیە</small>
            </div>
        </div>
    </div>
</div>

<!-- Section: Top Drivers & Top Vehicles -->
<div class="row g-4 mb-4">
    <!-- Top Drivers -->
    <div class="col-12 col-lg-6">
        <div class="card h-100 border-0 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">👤 چالاکترین شۆفێران</h5>
                <small class="text-muted">بەپێی کۆی گەشتەکان</small>
            </div>
            <div class="list-group list-group-flush">
                @forelse($topDrivers as $index => $d)
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center bg-transparent border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-secondary bg-opacity-10 text-body rounded-circle" style="width: 28px; height: 28px; line-height: 18px;">{{ $index + 1 }}</span>
                            <div>
                                <div class="fw-bold">{{ $d->name }}</div>
                                <small class="text-muted">{{ $d->email }}</small>
                            </div>
                        </div>
                        <span class="badge bg-primary rounded-pill px-3 py-2">{{ $d->movements_count }} گەشت</span>
                    </div>
                @empty
                    <div class="text-muted text-center py-3">داتا لەبەر دەست نییە.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Most Used Vehicles -->
    <div class="col-12 col-lg-6">
        <div class="card h-100 border-0 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">🚗 زۆرترین بەکارهێنانی ئۆتۆمبێل</h5>
                <small class="text-muted">بەپێی ژمارەی جوڵە</small>
            </div>
            <div class="list-group list-group-flush">
                @forelse($topVehicles as $index => $v)
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center bg-transparent border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-secondary bg-opacity-10 text-body rounded-circle" style="width: 28px; height: 28px; line-height: 18px;">{{ $index + 1 }}</span>
                            <div>
                                <div class="fw-bold">{{ $v->number }}</div>
                                <small class="text-muted">{{ $v->type }} {{ $v->model ? '('.$v->model.')' : '' }}</small>
                            </div>
                        </div>
                        <span class="badge bg-info text-dark rounded-pill px-3 py-2">{{ $v->movements_count }} جار</span>
                    </div>
                @empty
                    <div class="text-muted text-center py-3">داتا لەبەر دەست نییە.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Section: Recent Trips Activity -->
<div class="card border-0 shadow-sm overflow-hidden mb-4">
    <div class="card-header bg-transparent border-bottom p-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0">دوایین چالاکییەکان</h5>
            <small class="text-muted">کۆتا دەرچوون و گەڕانەوە تۆمارکراوەکان</small>
        </div>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('movements.index') }}" class="btn btn-sm btn-outline-primary">هەموو جوڵەکان <i class="bi bi-arrow-left"></i></a>
        @else
            <a href="{{ route('driver.history') }}" class="btn btn-sm btn-outline-primary">گەشتەکانی من <i class="bi bi-arrow-left"></i></a>
        @endif
    </div>

    <!-- Desktop Table -->
    <div class="responsive-table-desktop">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>شۆفێر</th>
                    <th>ئۆتۆمبێل</th>
                    <th>مەبەست</th>
                    <th>دەرچوون</th>
                    <th>گەڕانەوە</th>
                    <th>ماوە</th>
                    <th>دۆخ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent as $m)
                <tr>
                    <td class="fw-semibold">{{ $m->driver?->name ?? 'نادیار' }}</td>
                    <td class="fw-bold text-primary">{{ $m->vehicle?->number }}</td>
                    <td>{{ $m->destination }}</td>
                    <td>{{ $m->departure_time->format('Y-m-d H:i') }}</td>
                    <td>{{ $m->return_time?->format('Y-m-d H:i') ?? '—' }}</td>
                    <td>
                        @if($m->duration_minutes !== null)
                            {{ intdiv($m->duration_minutes, 60) }} کاتژمێر {{ $m->duration_minutes % 60 }} خولەک
                        @elseif($m->status === 'out')
                            <span class="text-warning fw-bold live-timer" data-start="{{ $m->departure_time->toISOString() }}">
                                {{ intdiv($m->current_duration_minutes, 60) }}:{{ str_pad($m->current_duration_minutes % 60, 2, '0', STR_PAD_LEFT) }}
                            </span>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $m->status === 'out' ? 'badge-out' : 'badge-returned' }}">
                            {{ $m->status === 'out' ? 'لە دەرەوە' : 'گەڕاوەتەوە' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">هیچ تۆمارێک نییە.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="p-3 d-lg-none">
        <div class="row g-3">
            @forelse($recent as $m)
            <div class="col-12 mobile-card-item">
                <div class="card p-3 border shadow-none bg-body-tertiary">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="fw-bold text-primary">{{ $m->vehicle?->number }}</span>
                        <span class="badge {{ $m->status === 'out' ? 'badge-out' : 'badge-returned' }}">
                            {{ $m->status === 'out' ? 'لە دەرەوە' : 'گەڕاوەتەوە' }}
                        </span>
                    </div>
                    <div class="small mb-1"><strong>شۆفێر:</strong> {{ $m->driver?->name }}</div>
                    <div class="small mb-1"><strong>مەبەست:</strong> {{ $m->destination }}</div>
                    <div class="small text-muted"><strong>دەرچوون:</strong> {{ $m->departure_time->format('Y-m-d H:i') }}</div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-3">هیچ تۆمارێک نییە.</div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Live Timer Updater: dynamically update every 10 seconds for active trips
    function updateLiveTimers() {
        document.querySelectorAll('.live-timer').forEach(el => {
            const startAttr = el.getAttribute('data-start');
            if (!startAttr) return;
            const start = new Date(startAttr);
            const now = new Date();
            const diffMinutes = Math.floor((now - start) / 60000);
            if (diffMinutes >= 0) {
                const h = Math.floor(diffMinutes / 60);
                const m = diffMinutes % 60;
                el.textContent = h + ' کاتژمێر ' + m + ' خولەک';
            }
        });
    }
    setInterval(updateLiveTimers, 10000);

    // Chart.js Configuration
    const ctx = document.getElementById('tripsChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($daysLabels) !!},
                datasets: [{
                    label: 'ژمارەی گەشتەکان',
                    data: {!! json_encode($daysData) !!},
                    backgroundColor: 'rgba(37, 99, 235, 0.75)',
                    borderColor: '#2563eb',
                    borderWidth: 1.5,
                    borderRadius: 8,
                    hoverBackgroundColor: '#1d4ed8'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }
</script>
@endpush
@endsection
