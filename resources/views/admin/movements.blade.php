@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">جوڵەی ئۆتۆمبێلەکان</h2>
        <p class="text-muted mb-0 small">تۆماری تەواوی دەرچوون و گەڕانەوەی هەموو ئۆتۆمبێلەکان</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('audit.index') }}">
            <i class="bi bi-shield-check me-1"></i> تۆماری چاودێری
        </a>
        <a class="btn btn-outline-primary" href="{{ route('reports.index') }}">
            <i class="bi bi-file-earmark-bar-graph me-1"></i> ڕاپۆرتەکان
        </a>
        <a class="btn btn-primary" href="{{ route('driver.departure') }}">
            <i class="bi bi-plus-circle me-1"></i> دەرچوونی نوێ
        </a>
    </div>
</div>

<!-- Section: Overdue Vehicles (ئۆتۆمبێلە نەگەڕاوەکان) -->
<div id="not-returned" class="card mb-4 border-0 shadow-sm overflow-hidden {{ $notReturned->count() > 0 ? 'border-warning border-start border-4' : '' }}">
    <div class="card-header bg-transparent p-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <span class="p-2 rounded-circle bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock-history fs-5"></i></span>
            <div>
                <h5 class="fw-bold mb-0 text-body">ئۆتۆمبێلە نەگەڕاوەکان ({{ $notReturned->count() }})</h5>
                <small class="text-muted">چاودێری ماوەی لە دەرەوە بوون و ئاستی دواکەوتن</small>
            </div>
        </div>
        @if($notReturned->count() > 0)
            <span class="badge bg-warning text-dark">{{ $notReturned->count() }} لە دەرەوە</span>
        @endif
    </div>

    @if($notReturned->isEmpty())
        <div class="p-4 text-center text-muted">
            <i class="bi bi-check-circle-fill fs-2 text-success d-block mb-1"></i>
            <span class="fw-bold">هەموو ئۆتۆمبێلەکان گەڕاونەتەوە. ✅</span>
        </div>
    @else
        <!-- Desktop Table -->
        <div class="responsive-table-desktop">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>شۆفێر</th>
                        <th>ژمارەی ئۆتۆمبێل</th>
                        <th>جۆری ئۆتۆمبێل</th>
                        <th>مەبەست</th>
                        <th>کاتی دەرچوون</th>
                        <th>ماوەی ئێستا (Live)</th>
                        <th>ئاستی ئاگاداری</th>
                        <th class="text-end">کردار</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notReturned as $m)
                        @php
                            $minutes = $m->current_duration_minutes;
                            $overdue = $m->overdue_status;
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $m->driver?->name ?? '—' }}</div>
                                <small class="text-muted">{{ $m->driver?->phone ?: '—' }}</small>
                            </td>
                            <td class="fw-bold text-primary">{{ $m->vehicle?->number }}</td>
                            <td>{{ $m->vehicle?->type }}</td>
                            <td>{{ $m->destination }}</td>
                            <td>{{ $m->departure_time->format('Y-m-d H:i') }}</td>
                            <td>
                                <span class="fw-bold live-timer" data-start="{{ $m->departure_time->toISOString() }}">
                                    {{ intdiv($minutes, 60) }} کاتژمێر {{ $minutes % 60 }} خولەک
                                </span>
                            </td>
                            <td>
                                @if($overdue === 'warning')
                                    <span class="badge-danger-glow">⚠️ دواکەوتوو (مەترسی)</span>
                                @elseif($overdue === 'attention')
                                    <span class="badge-warning-glow">⚠️ سەرنج پێویستە</span>
                                @else
                                    <span class="badge-normal">ئاسایی</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('driver.return', $m) }}" onsubmit="return confirm('تۆمارکردنی گەڕانەوە بۆ ئەم ئۆتۆمبێلە؟')">
                                    @csrf
                                    <button class="btn btn-sm btn-success">
                                        <i class="bi bi-check2"></i> گەڕانەوە
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="p-3 d-lg-none">
            <div class="row g-3">
                @foreach($notReturned as $m)
                    @php
                        $minutes = $m->current_duration_minutes;
                        $overdue = $m->overdue_status;
                    @endphp
                    <div class="col-12 mobile-card-item">
                        <div class="card p-3 border shadow-none bg-body-tertiary">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-primary mb-0">{{ $m->vehicle?->number }}</h6>
                                @if($overdue === 'warning')
                                    <span class="badge-danger-glow" style="font-size: 11px;">⚠️ مەترسی</span>
                                @elseif($overdue === 'attention')
                                    <span class="badge-warning-glow" style="font-size: 11px;">⚠️ سەرنج</span>
                                @else
                                    <span class="badge-normal" style="font-size: 11px;">ئاسایی</span>
                                @endif
                            </div>
                            <div class="small mb-1"><strong>شۆفێر:</strong> {{ $m->driver?->name }}</div>
                            <div class="small mb-1"><strong>مەبەست:</strong> {{ $m->destination }}</div>
                            <div class="small mb-2">
                                <strong>ماوە:</strong>
                                <span class="fw-bold text-primary live-timer" data-start="{{ $m->departure_time->toISOString() }}">
                                    {{ intdiv($minutes, 60) }} کاتژمێر {{ $minutes % 60 }} خولەک
                                </span>
                            </div>
                            <form method="POST" action="{{ route('driver.return', $m) }}" onsubmit="return confirm('تۆمارکردنی گەڕانەوە بۆ {{ $m->vehicle?->number }}؟')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success w-100">
                                    <i class="bi bi-check-circle me-1"></i> تۆمارکردنی گەڕانەوە
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Section: Filter & Search Movements -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('movements.index') }}" id="movementsFilterForm" class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold">گەڕان بە دەق</label>
                <div class="input-group">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" id="liveSearchInput" class="form-control border-start-0" placeholder="مەبەست، شۆفێر، ژمارە..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold">شۆفێر</label>
                <select name="driver_id" class="form-select filter-trigger">
                    <option value="">هەموو شۆفێران</option>
                    @foreach($drivers as $d)
                        <option value="{{ $d->id }}" @selected(request('driver_id') == $d->id)>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold">ئۆتۆمبێل</label>
                <select name="vehicle_id" class="form-select filter-trigger">
                    <option value="">هەموو ئۆتۆمبێلەکان</option>
                    @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" @selected(request('vehicle_id') == $v->id)>{{ $v->number }} ({{ $v->type }})</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold">دۆخی گەشت</label>
                <select name="status" class="form-select filter-trigger">
                    <option value="">هەموو دۆخەکان</option>
                    <option value="out" @selected(request('status') === 'out')>لە دەرەوەیە</option>
                    <option value="returned" @selected(request('status') === 'returned')>گەڕاوەتەوە</option>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold">بەروار</label>
                <input type="date" name="date" class="form-control filter-trigger" value="{{ request('date') }}">
            </div>

            <div class="col-12 col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100" title="فلتەر"><i class="bi bi-funnel"></i></button>
                <a href="{{ route('movements.index') }}" class="btn btn-light" title="سڕینەوەی فلتەر"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Section: All Movements Results Table & Mobile View -->
<div class="card border-0 shadow-sm overflow-hidden" id="movementsTableContainer">
    @include('admin.movements_table_partial')
</div>

@push('scripts')
<script>
    // Live AJAX Filtering without full page reload
    const form = document.getElementById('movementsFilterForm');
    const container = document.getElementById('movementsTableContainer');
    const searchInput = document.getElementById('liveSearchInput');

    function applyFilterAjax() {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();
        window.history.replaceState({}, '', `${window.location.pathname}?${params}`);

        container.style.opacity = '0.5';

        fetch(`${window.location.pathname}?${params}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            container.innerHTML = html;
            container.style.opacity = '1';
        })
        .catch(() => {
            container.style.opacity = '1';
        });
    }

    let searchDebounce = null;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(applyFilterAjax, 400);
    });

    document.querySelectorAll('.filter-trigger').forEach(el => {
        el.addEventListener('change', applyFilterAjax);
    });
</script>
@endpush
@endsection
