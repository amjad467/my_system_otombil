@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-1">گەڕانی گشتی لە سیستەم</h2>
    <p class="text-muted mb-0 small">دۆزینەوەی شۆفێران، ئۆتۆمبێلەکان، شوێن و گەشتەکان</p>
</div>

<!-- Search Input Box -->
<div class="card border-0 shadow-sm p-3 mb-4">
    <form method="GET" action="{{ route('search') }}">
        <div class="input-group input-group-lg">
            <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-primary"></i></span>
            <input type="text" name="q" class="form-control border-start-0 shadow-none fs-5" placeholder="دەستەواژەی گەڕان بنووسە..." value="{{ $query }}" autofocus>
            <button class="btn btn-primary px-4" type="submit">گەڕان</button>
        </div>
    </form>
</div>

@if(!empty($query))
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold mb-0">ئەنجامەکانی گەڕان بۆ: <span class="text-primary">"{{ $query }}"</span></h5>
    </div>

    <!-- Vehicles Results -->
    @if($vehicles->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent p-3 fw-bold"><i class="bi bi-car-front text-primary me-2"></i> ئۆتۆمبێلەکان ({{ $vehicles->count() }})</div>
            <div class="list-group list-group-flush">
                @foreach($vehicles as $v)
                    <div class="list-group-item p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold fs-6 text-primary">{{ $v->number }}</span>
                            <span class="text-muted ms-2">({{ $v->type }} {{ $v->model }})</span>
                            @if($v->description)
                                <div class="small text-muted">{{ $v->description }}</div>
                            @endif
                        </div>
                        <span class="badge {{ $v->status_badge_class }}">{{ $v->status_label }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Drivers Results -->
    @if($drivers->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent p-3 fw-bold"><i class="bi bi-people text-primary me-2"></i> بەکارهێنەر و شۆفێران ({{ $drivers->count() }})</div>
            <div class="list-group list-group-flush">
                @foreach($drivers as $d)
                    <div class="list-group-item p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold">{{ $d->name }}</span>
                            <span class="text-muted ms-2">{{ $d->phone ?: $d->email }}</span>
                        </div>
                        <span class="badge {{ $d->status_badge_class }}">{{ $d->status_label }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Trips / Movements Results -->
    @if($movements->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent p-3 fw-bold"><i class="bi bi-geo-alt text-primary me-2"></i> گەشتەکان ({{ $movements->count() }})</div>
            <div class="list-group list-group-flush">
                @foreach($movements as $m)
                    <div class="list-group-item p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold">{{ $m->destination }}</div>
                            <small class="text-muted">شۆفێر: {{ $m->driver?->name }} | ئۆتۆمبێل: {{ $m->vehicle?->number }} ({{ $m->departure_time->format('Y-m-d H:i') }})</small>
                        </div>
                        <span class="badge {{ $m->status === 'out' ? 'badge-out' : 'badge-returned' }}">
                            {{ $m->status === 'out' ? 'لە دەرەوە' : 'گەڕاوەتەوە' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Notifications Results -->
    @if($notifications->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent p-3 fw-bold"><i class="bi bi-bell text-primary me-2"></i> ئاگادارکردنەوەکان ({{ $notifications->count() }})</div>
            <div class="list-group list-group-flush">
                @foreach($notifications as $n)
                    <div class="list-group-item p-3">
                        <div class="fw-bold">{{ $n->data['title'] ?? 'ئاگادارکردنەوە' }}</div>
                        <div class="text-muted small">{{ $n->data['body'] ?? '' }}</div>
                        <small class="text-secondary">{{ $n->created_at->diffForHumans() }}</small>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($vehicles->isEmpty() && $drivers->isEmpty() && $movements->isEmpty() && $notifications->isEmpty())
        <div class="card border-0 shadow-sm p-5 text-center text-muted">
            <i class="bi bi-emoji-neutral fs-1 opacity-25 d-block mb-2"></i>
            <h5 class="fw-bold">هیچ ئەنجامێک بۆ "{{ $query }}" نەدۆزرایەوە.</h5>
            <p class="small text-muted mb-0">وشەی تر تاقی بکەرەوە، وەک ناوی شۆفێر، ژمارەی ئۆتۆمبێل، یان شوێنی گەشت.</p>
        </div>
    @endif
@endif
@endsection
