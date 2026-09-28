@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">ڕاپۆرتی جوڵەی ئۆتۆمبێلەکان</h2>
        <p class="text-muted mb-0 small">دروستکردن، فلتەرکردن، داگرتن و چاپی ڕاپۆرتە وردەکانی سیستەم</p>
    </div>
    <div class="d-flex flex-wrap gap-2 no-print">
        <a class="btn btn-success d-flex align-items-center gap-2" href="{{ route('reports.csv', request()->query()) }}">
            <i class="bi bi-file-earmark-excel-fill"></i>
            <span>داگرتنی Excel / CSV</span>
        </a>
        <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2" onclick="window.print()">
            <i class="bi bi-printer-fill"></i>
            <span>چاپکردنی ڕاپۆرت (Print / PDF)</span>
        </button>
    </div>
</div>

<!-- Printable Header (Visible Only on Print) -->
<div class="d-none d-print-block text-center mb-4 pb-3 border-bottom">
    <h3 class="fw-bold mb-1">{{ \App\Models\Setting::get('org_name', 'سیستەمی بەڕێوەبردنی ئۆتۆمبێلەکان') }}</h3>
    <h4>ڕاپۆرتی فەرمیی جوڵە و گەشتەکانی ئۆتۆمبێل</h4>
    <small class="text-muted">بەرواری دەرکردنی ڕاپۆرت: {{ now()->format('Y-m-d H:i') }} | بەکارهێنەر: {{ auth()->user()->name }}</small>
</div>

<!-- Summary Metric Cards (Dynamic based on filter) -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card p-3 border-0 shadow-sm text-center">
            <span class="text-muted small">کۆی گەشتەکان</span>
            <div class="fs-3 fw-bold text-primary">{{ $summary['total_trips'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 border-0 shadow-sm text-center">
            <span class="text-muted small">گەشتە گەڕاوەکان</span>
            <div class="fs-3 fw-bold text-success">{{ $summary['completed'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 border-0 shadow-sm text-center">
            <span class="text-muted small">کۆی کاتژمێری گەشتەکان</span>
            <div class="fs-3 fw-bold text-info">{{ $summary['total_hours'] }} کاتژمێر</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 border-0 shadow-sm text-center">
            <span class="text-muted small">ناوەندی ماوەی گەشت</span>
            <div class="fs-3 fw-bold text-secondary">{{ $summary['avg_minutes'] }} خولەک</div>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="card border-0 shadow-sm mb-4 no-print">
    <div class="card-body p-4">
        <!-- Quick Preset Periods -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3 pb-3 border-bottom">
            <span class="text-muted small fw-bold">ماوەی خێرا:</span>
            <a href="{{ route('reports.index', array_merge(request()->except(['from','to','page']), ['preset' => 'today'])) }}" class="btn btn-sm {{ (request('preset') === 'today') ? 'btn-primary' : 'btn-outline-secondary' }}">ئەمڕۆ</a>
            <a href="{{ route('reports.index', array_merge(request()->except(['from','to','page']), ['preset' => 'week'])) }}" class="btn btn-sm {{ (request('preset') === 'week') ? 'btn-primary' : 'btn-outline-secondary' }}">ئەم هەفتەیە</a>
            <a href="{{ route('reports.index', array_merge(request()->except(['from','to','page']), ['preset' => 'month'])) }}" class="btn btn-sm {{ (request('preset') === 'month') ? 'btn-primary' : 'btn-outline-secondary' }}">ئەم مانگە</a>
            <a href="{{ route('reports.index', array_merge(request()->except(['from','to','page']), ['preset' => 'year'])) }}" class="btn btn-sm {{ (request('preset') === 'year') ? 'btn-primary' : 'btn-outline-secondary' }}">ئەمساڵ</a>
        </div>

        <form method="GET" action="{{ route('reports.index') }}" class="row g-3">
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold">لە بەرواری</label>
                <input class="form-control" type="date" name="from" value="{{ $filters['from'] ?? '' }}">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold">تا بەرواری</label>
                <input class="form-control" type="date" name="to" value="{{ $filters['to'] ?? '' }}">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold">شۆفێر</label>
                <select class="form-select" name="driver_id">
                    <option value="">هەموو شۆفێران</option>
                    @foreach($drivers as $d)
                        <option value="{{ $d->id }}" @selected(($filters['driver_id'] ?? '') == $d->id)>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold">ئۆتۆمبێل</label>
                <select class="form-select" name="vehicle_id">
                    <option value="">هەموو ئۆتۆمبێلەکان</option>
                    @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" @selected(($filters['vehicle_id'] ?? '') == $v->id)>{{ $v->number }} ({{ $v->type }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold">دۆخ</label>
                <select class="form-select" name="status">
                    <option value="">هەموو دۆخەکان</option>
                    <option value="out" @selected(($filters['status'] ?? '') === 'out')>لە دەرەوە</option>
                    <option value="returned" @selected(($filters['status'] ?? '') === 'returned')>گەڕاوەتەوە</option>
                </select>
            </div>
            <div class="col-6 col-md-6">
                <label class="form-label small fw-semibold">مەبەست یان شوێن</label>
                <input class="form-control" name="destination" placeholder="بەشێک لە ناوی شوێن بنووسە..." value="{{ $filters['destination'] ?? '' }}">
            </div>
            <div class="col-12 col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i> فلتەرکردن</button>
                <a class="btn btn-light" href="{{ route('reports.index') }}">پاککردنەوە</a>
            </div>
        </form>
    </div>
</div>

<!-- Report Table (User requirement 10: Driver | Vehicle | Type | Destination | Departure | Return | Duration | Status) -->
<div class="card border-0 shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>شۆفێر</th>
                    <th>ژمارەی ئۆتۆمبێل</th>
                    <th>جۆری ئۆتۆمبێل</th>
                    <th>مەبەست</th>
                    <th>کاتی دەرچوون</th>
                    <th>کاتی گەڕانەوە</th>
                    <th>ماوە</th>
                    <th>دۆخ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $m)
                <tr>
                    <td class="fw-semibold">{{ $m->driver?->name ?? '—' }}</td>
                    <td class="fw-bold text-primary">{{ $m->vehicle?->number ?? '—' }}</td>
                    <td>{{ $m->vehicle?->type ?? '—' }}</td>
                    <td>
                        <div>{{ $m->destination }}</div>
                        @if($m->purpose)
                            <small class="text-muted">{{ $m->purpose }}</small>
                        @endif
                    </td>
                    <td>
                        <div>{{ $m->departure_time->format('H:i') }}</div>
                        <small class="text-muted">{{ $m->departure_time->format('Y-m-d') }}</small>
                    </td>
                    <td>
                        @if($m->return_time)
                            <div>{{ $m->return_time->format('H:i') }}</div>
                            <small class="text-muted">{{ $m->return_time->format('Y-m-d') }}</small>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($m->duration_minutes !== null)
                            <span>{{ intdiv($m->duration_minutes, 60) }} کاتژمێر {{ $m->duration_minutes % 60 }} خولەک</span>
                        @elseif($m->status === 'out')
                            @php $live = \Carbon\Carbon::now()->diffInMinutes($m->departure_time); @endphp
                            <span class="text-warning fw-bold">{{ intdiv($live, 60) }} کاتژمێر {{ $live % 60 }} خولەک</span>
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
                <tr><td colspan="8" class="text-center text-muted py-5">هیچ داتایەک نەدۆزرایەوە.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3 border-top no-print">
        {{ $movements->links() }}
    </div>
</div>
@endsection
