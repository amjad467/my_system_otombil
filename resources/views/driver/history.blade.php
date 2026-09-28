@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">مێژووی گەشتەکانم</h2>
        <p class="text-muted mb-0 small">تۆماری تەواوی دەرچوون و گەڕانەوەکانی تایبەت بە تۆ</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="{{ route('driver.active') }}">
            <i class="bi bi-geo-alt"></i> گەشتی کراوە
        </a>
        <a class="btn btn-primary" href="{{ route('driver.departure') }}">
            <i class="bi bi-plus-circle"></i> دەرچوونی نوێ
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-6">
                <label class="form-label small fw-semibold">گەڕان</label>
                <div class="input-group">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="مەبەست، ژمارەی ئۆتۆمبێل، هۆکار..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label small fw-semibold">دۆخ</label>
                <select name="status" class="form-select">
                    <option value="">هەموو گەشتەکان</option>
                    <option value="out" @selected(request('status') === 'out')>لە دەرەوەیە (تەواونەکراو)</option>
                    <option value="returned" @selected(request('status') === 'returned')>گەڕاوەتەوە (تەواوبوو)</option>
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> فلتەر</button>
                <a href="{{ route('driver.history') }}" class="btn btn-light"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <!-- Desktop Table View -->
    <div class="responsive-table-desktop">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>ئۆتۆمبێل</th>
                    <th>مەبەست</th>
                    <th>هۆکار</th>
                    <th>دەرچوون</th>
                    <th>گەڕانەوە</th>
                    <th>ماوە</th>
                    <th>دۆخ</th>
                    <th class="text-end">کردار</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $m)
                <tr>
                    <td>
                        <div class="fw-bold text-primary">{{ $m->vehicle?->number }}</div>
                        <small class="text-muted">{{ $m->vehicle?->type }}</small>
                    </td>
                    <td class="fw-semibold">{{ $m->destination }}</td>
                    <td><small class="text-muted">{{ $m->purpose ?: '—' }}</small></td>
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
                            <span class="badge bg-warning text-dark live-timer" data-start="{{ $m->departure_time->toISOString() }}">
                                {{ intdiv($m->current_duration_minutes, 60) }} کاتژمێر {{ $m->current_duration_minutes % 60 }} خولەک
                            </span>
                        @else — @endif
                    </td>
                    <td>
                        <span class="badge {{ $m->status === 'out' ? 'badge-out' : 'badge-returned' }}">
                            {{ $m->status === 'out' ? 'لە دەرەوە' : 'گەڕاوەتەوە' }}
                        </span>
                    </td>
                    <td class="text-end">
                        @if($m->status === 'out')
                            <form method="POST" action="{{ route('driver.return', $m) }}" onsubmit="return confirm('تۆمارکردنی گەڕانەوە بۆ ئەم ئۆتۆمبێلە؟')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="bi bi-check2"></i> گەڕانەوە
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-5">هیچ گەشتێک نەدۆزرایەوە.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View (User requirement 2: Trip card view on mobile) -->
    <div class="p-3 d-lg-none">
        <div class="row g-3">
            @forelse($movements as $m)
            <div class="col-12 mobile-card-item">
                <div class="card p-3 border shadow-none bg-body-tertiary">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-bold mb-0 text-primary">{{ $m->vehicle?->number }}</h6>
                            <small class="text-muted">{{ $m->vehicle?->type }}</small>
                        </div>
                        <span class="badge {{ $m->status === 'out' ? 'badge-out' : 'badge-returned' }}">
                            {{ $m->status === 'out' ? 'لە دەرەوە' : 'گەڕاوەتەوە' }}
                        </span>
                    </div>

                    <div class="bg-body p-2 rounded-3 mb-2 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">مەبەست:</span>
                            <span class="fw-semibold">{{ $m->destination }}</span>
                        </div>
                        @if($m->purpose)
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">هۆکار:</span>
                            <span>{{ $m->purpose }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">دەرچوون:</span>
                            <span>{{ $m->departure_time->format('Y-m-d H:i') }}</span>
                        </div>
                        @if($m->return_time)
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">گەڕانەوە:</span>
                            <span>{{ $m->return_time->format('Y-m-d H:i') }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">ماوە:</span>
                            <span class="fw-bold">
                                @if($m->duration_minutes !== null)
                                    {{ intdiv($m->duration_minutes, 60) }} کاتژمێر {{ $m->duration_minutes % 60 }} خولەک
                                @elseif($m->status === 'out')
                                    <span class="text-warning live-timer" data-start="{{ $m->departure_time->toISOString() }}">
                                        {{ intdiv($m->current_duration_minutes, 60) }} کاتژمێر {{ $m->current_duration_minutes % 60 }} خولەک
                                    </span>
                                @else — @endif
                            </span>
                        </div>
                    </div>

                    @if($m->status === 'out')
                        <form method="POST" action="{{ route('driver.return', $m) }}" onsubmit="return confirm('تۆمارکردنی گەڕانەوە بۆ ئەم گەشتە؟')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success w-100">
                                <i class="bi bi-check-circle me-1"></i> تۆمارکردنی گەڕانەوە
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-4">هیچ گەشتێک لەم بەشەدا نییە.</div>
            @endforelse
        </div>
    </div>

    <div class="p-3 border-top">
        {{ $movements->links() }}
    </div>
</div>
@endsection
