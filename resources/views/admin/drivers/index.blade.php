@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">بەکارهێنەران و شۆفێران</h2>
        <p class="text-muted mb-0 small">بەڕێوەبردنی هەژمارەکان، ڕۆڵەکان و دۆخی بەردەستبوونی شۆفێران</p>
    </div>
    <a class="btn btn-primary d-flex align-items-center gap-2" href="{{ route('drivers.create') }}">
        <i class="bi bi-person-plus-fill"></i>
        <span>زیادکردنی بەکارهێنەری نوێ</span>
    </a>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <label class="form-label small fw-semibold">گەڕان</label>
                <div class="input-group">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="ناو، ئیمەیڵ، ژمارەی تەلەفۆن..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold">ڕۆڵ</label>
                <select name="role" class="form-select">
                    <option value="">هەموو ڕۆڵەکان</option>
                    <option value="driver" @selected(request('role') === 'driver')>شۆفێری ئاسایی</option>
                    <option value="admin" @selected(request('role') === 'admin')>بەڕێوەبەر (Admin)</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold">دۆخ</label>
                <select name="status" class="form-select">
                    <option value="">هەموو</option>
                    <option value="available" @selected(request('status') === 'available')>بەردەست</option>
                    <option value="on_trip" @selected(request('status') === 'on_trip')>لە گەشتدایە</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>ناچالاک</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> فلتەر</button>
                <a href="{{ route('drivers.index') }}" class="btn btn-light"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Users List -->
<div class="card border-0 shadow-sm overflow-hidden">
    <!-- Desktop Table -->
    <div class="responsive-table-desktop">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>ناو و بەکارهێنەر</th>
                    <th>زانیاری پەیوەندی</th>
                    <th>ڕۆڵ</th>
                    <th>دۆخی شۆفێر</th>
                    <th>کۆی گەشتەکان</th>
                    <th class="text-end">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                @forelse($drivers as $d)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                                {{ mb_substr($d->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-bold">{{ $d->name }}</div>
                                <small class="text-muted">{{ $d->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>{{ $d->phone ?: '—' }}</div>
                    </td>
                    <td>
                        @if($d->role === 'admin')
                            <span class="badge bg-primary">بەڕێوەبەر</span>
                        @else
                            <span class="badge bg-secondary">شۆفێر</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $d->status_badge_class }}">
                            {{ $d->status_label }}
                        </span>
                        @if($d->effective_status === 'on_trip')
                            <div class="small text-muted mt-1">
                                بەرەو: {{ $d->activeMovement?->destination }}
                            </div>
                        @endif
                    </td>
                    <td>
                        <span class="fw-bold">{{ $d->total_trips }} گەشت</span>
                    </td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('drivers.edit', $d) }}" title="دەستکاری">
                            <i class="bi bi-pencil"></i> دەستکاری
                        </a>
                        @if($d->id !== auth()->id())
                            <form class="d-inline" method="POST" action="{{ route('drivers.destroy', $d) }}" onsubmit="return confirm('ئایا دڵنیایت لە گۆڕینی دۆخی چالاکیی ئەم بەکارهێنەرە؟')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm {{ $d->active ? 'btn-outline-danger' : 'btn-outline-success' }}" title="{{ $d->active ? 'ناچالاککردن' : 'چالاککردنەوە' }}">
                                    <i class="bi {{ $d->active ? 'bi-person-x' : 'bi-person-check' }}"></i>
                                    {{ $d->active ? 'ناچالاککردن' : 'چالاککردن' }}
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-5">هیچ بەکارهێنەرێک نەدۆزرایەوە.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View (User requirement 2: Driver card view on mobile) -->
    <div class="p-3 d-lg-none">
        <div class="row g-3">
            @forelse($drivers as $d)
            <div class="col-12 mobile-card-item">
                <div class="card p-3 border shadow-none bg-body-tertiary">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                                {{ mb_substr($d->name, 0, 1) }}
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">{{ $d->name }}</h6>
                                <span class="badge bg-secondary" style="font-size: 10px;">{{ $d->role === 'admin' ? 'بەڕێوەبەر' : 'شۆفێر' }}</span>
                            </div>
                        </div>
                        <span class="badge {{ $d->status_badge_class }}">
                            {{ $d->status_label }}
                        </span>
                    </div>

                    <div class="bg-body p-2 rounded-3 mb-2 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">مۆبایل:</span>
                            <span>{{ $d->phone ?: '—' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">ئیمەیڵ:</span>
                            <span class="text-truncate" style="max-width: 180px;">{{ $d->email }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">ژمارەی گەشت:</span>
                            <span class="fw-bold">{{ $d->total_trips }} گەشت</span>
                        </div>
                        @if($d->effective_status === 'on_trip')
                        <div class="d-flex justify-content-between text-warning">
                            <span>کاتی دەرچوون:</span>
                            <span>{{ $d->activeMovement?->departure_time->format('Y-m-d H:i') }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                        <a class="btn btn-sm btn-outline-primary flex-grow-1" href="{{ route('drivers.edit', $d) }}">
                            <i class="bi bi-pencil me-1"></i> دەستکاری
                        </a>
                        @if($d->id !== auth()->id())
                            <form class="flex-grow-1" method="POST" action="{{ route('drivers.destroy', $d) }}" onsubmit="return confirm('گۆڕینی دۆخی بەکارهێنەر؟')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm {{ $d->active ? 'btn-outline-danger' : 'btn-outline-success' }} w-100">
                                    {{ $d->active ? 'ناچالاککردن' : 'چالاککردنەوە' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-4">هیچ بەکارهێنەرێک نییە.</div>
            @endforelse
        </div>
    </div>

    <div class="p-3 border-top">
        {{ $drivers->links() }}
    </div>
</div>
@endsection
