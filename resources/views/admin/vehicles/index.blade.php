@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">ئۆتۆمبێلەکان</h2>
        <p class="text-muted mb-0 small">بەڕێوەبردنی ئۆتۆمبێلەکانی دامەزراوە، دۆخی بەردەستبوون و چاککردنەوە</p>
    </div>
    <a class="btn btn-primary d-flex align-items-center gap-2" href="{{ route('vehicles.create') }}">
        <i class="bi bi-plus-circle-fill"></i>
        <span>زیادکردنی ئۆتۆمبێلی نوێ</span>
    </a>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-6">
                <label class="form-label small fw-semibold">گەڕان</label>
                <div class="input-group">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="ژمارەی ئۆتۆمبێل، جۆر، مۆدێل..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label small fw-semibold">دۆخ</label>
                <select name="status" class="form-select">
                    <option value="">هەموو دۆخەکان</option>
                    <option value="available" @selected(request('status') === 'available')>بەردەست</option>
                    <option value="on_trip" @selected(request('status') === 'on_trip')>لە گەشتدایە</option>
                    <option value="maintenance" @selected(request('status') === 'maintenance')>لە چاککردنەوەدایە (Maintenance)</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>ناچالاک</option>
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> فلتەر</button>
                <a href="{{ route('vehicles.index') }}" class="btn btn-light"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Vehicles List -->
<div class="card border-0 shadow-sm overflow-hidden">
    <!-- Desktop Table View -->
    <div class="responsive-table-desktop">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>ژمارەی ئۆتۆمبێل</th>
                    <th>جۆر و مۆدێل</th>
                    <th>دۆخی ئۆتۆمبێل</th>
                    <th>شۆفێری ئێستا</th>
                    <th>کۆی بەکارهێنان</th>
                    <th class="text-end">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicles as $v)
                <tr>
                    <td>
                        <div class="fw-bold fs-6 text-primary">{{ $v->number }}</div>
                        @if($v->description)
                            <small class="text-muted">{{ Str::limit($v->description, 35) }}</small>
                        @endif
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $v->type }}</div>
                        <small class="text-muted">{{ $v->model ?: '—' }}</small>
                    </td>
                    <td>
                        <span class="badge {{ $v->status_badge_class }}">
                            {{ $v->status_label }}
                        </span>
                    </td>
                    <td>
                        @if($v->activeMovement)
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-warning text-dark">{{ $v->activeMovement->driver?->name }}</span>
                                <small class="text-muted">بەرەو {{ $v->activeMovement->destination }}</small>
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="fw-bold">{{ $v->movements_count }} گەشت</span>
                    </td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('vehicles.edit', $v) }}" title="دەستکاری">
                            <i class="bi bi-pencil"></i> دەستکاری
                        </a>
                        <form class="d-inline" method="POST" action="{{ route('vehicles.destroy', $v) }}" onsubmit="return confirm('ئایا دڵنیایت لە گۆڕینی دۆخی ئەم ئۆتۆمبێلە؟')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm {{ $v->active ? 'btn-outline-danger' : 'btn-outline-success' }}" title="{{ $v->active ? 'ناچالاککردن' : 'چالاککردنەوە' }}">
                                <i class="bi {{ $v->active ? 'bi-slash-circle' : 'bi-check-circle' }}"></i>
                                {{ $v->active ? 'ناچالاک' : 'چالاککردن' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-5">هیچ ئۆتۆمبێلێک بە پێی ئەم فلتەرە نەدۆزرایەوە.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View (User requirement 2: Vehicle card view on mobile) -->
    <div class="p-3 d-lg-none">
        <div class="row g-3">
            @forelse($vehicles as $v)
            <div class="col-12 mobile-card-item">
                <div class="card p-3 border shadow-none bg-body-tertiary">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-bold mb-0 text-primary">{{ $v->number }}</h6>
                            <small class="text-muted">{{ $v->type }} {{ $v->model ? '('.$v->model.')' : '' }}</small>
                        </div>
                        <span class="badge {{ $v->status_badge_class }}">
                            {{ $v->status_label }}
                        </span>
                    </div>

                    <div class="bg-body p-2 rounded-3 mb-2 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">شۆفێری ئێستا:</span>
                            <span class="fw-bold">{{ $v->activeMovement ? $v->activeMovement->driver?->name : 'هیچ شۆفێرێک (بەردەست)' }}</span>
                        </div>
                        @if($v->activeMovement)
                        <div class="d-flex justify-content-between mb-1 text-warning">
                            <span class="text-muted">مەبەست:</span>
                            <span>{{ $v->activeMovement->destination }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">کۆی بەکارهێنان:</span>
                            <span class="fw-semibold">{{ $v->movements_count }} گەشت</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a class="btn btn-sm btn-outline-primary flex-grow-1" href="{{ route('vehicles.edit', $v) }}">
                            <i class="bi bi-pencil me-1"></i> دەستکاری
                        </a>
                        <form class="flex-grow-1" method="POST" action="{{ route('vehicles.destroy', $v) }}" onsubmit="return confirm('گۆڕینی دۆخی ئەم ئۆتۆمبێلە؟')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm {{ $v->active ? 'btn-outline-danger' : 'btn-outline-success' }} w-100">
                                {{ $v->active ? 'ناچالاککردن' : 'چالاککردنەوە' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-4">هیچ ئۆتۆمبێلێک نییە.</div>
            @endforelse
        </div>
    </div>

    <div class="p-3 border-top">
        {{ $vehicles->links() }}
    </div>
</div>
@endsection
