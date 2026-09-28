@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">📋 تۆماری چاودێری (Audit Log)</h2>
        <p class="text-muted mb-0 small">تۆمارکردنی هەموو کردار، چوونەژوورەوە، گۆڕانکاری و تێپەڕاندنەکانی ناو سیستەم</p>
    </div>
    <a href="{{ route('movements.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left me-1"></i> گەڕانەوە بۆ جوڵەکان
    </a>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('audit.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold">گەڕان</label>
                <div class="input-group">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="بەکارهێنەر، وردەکاری..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold">جۆری کردار</label>
                <select name="action" class="form-select">
                    <option value="">هەموو کردارەکان</option>
                    @foreach($actions as $key => $label)
                        <option value="{{ $key }}" @selected(request('action') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold">بەکارهێنەر</label>
                <select name="user_id" class="form-select">
                    <option value="">هەموو بەکارهێنەران</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold">بەروار</label>
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>

            <div class="col-6 col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i></button>
                <a class="btn btn-light" href="{{ route('audit.index') }}"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>کاتی کردار</th>
                    <th>بەکارهێنەر</th>
                    <th>جۆری کردار</th>
                    <th>وردەکاری</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="text-nowrap" style="width: 170px;">
                        <div>{{ $log->performed_at->format('H:i:s') }}</div>
                        <small class="text-muted">{{ $log->performed_at->format('Y-m-d') }}</small>
                    </td>
                    <td>
                        <div class="fw-bold">{{ $log->user?->name ?? 'سیستەم / سڕاوەتەوە' }}</div>
                        <small class="text-muted">{{ $log->user?->role === 'admin' ? 'بەڕێوەبەر' : ($log->user ? 'شۆفێر' : '') }}</small>
                    </td>
                    <td>
                        @php
                            $badgeMap = [
                                'user_login'           => 'bg-info text-dark',
                                'user_logout'          => 'bg-secondary',
                                'departure_created'    => 'bg-primary',
                                'departure_overridden' => 'bg-danger',
                                'return_recorded'      => 'bg-success',
                                'user_created'         => 'bg-primary',
                                'user_updated'         => 'bg-warning text-dark',
                                'user_deactivated'     => 'bg-danger',
                                'user_activated'       => 'bg-success',
                                'vehicle_created'      => 'bg-primary',
                                'vehicle_updated'      => 'bg-warning text-dark',
                                'vehicle_deactivated'  => 'bg-danger',
                                'vehicle_activated'    => 'bg-success',
                                'backup_created'       => 'bg-info text-dark',
                                'backup_downloaded'    => 'bg-info text-dark',
                                'backup_restored'      => 'bg-danger',
                                'backup_deleted'       => 'bg-danger',
                                'settings_updated'     => 'bg-secondary',
                            ];
                        @endphp
                        <span class="badge {{ $badgeMap[$log->action] ?? 'bg-secondary' }}">
                            {{ $actions[$log->action] ?? $log->action }}
                        </span>
                    </td>
                    <td>
                        @if($log->details && is_array($log->details))
                            <div class="small">
                                @foreach($log->details as $k => $v)
                                    <span class="badge bg-body-secondary text-body border me-1 mb-1">
                                        <strong>{{ $k }}:</strong> 
                                        @if(is_bool($v))
                                            {{ $v ? 'بەڵێ' : 'نەخێر' }}
                                        @elseif(is_array($v))
                                            {{ json_encode($v, JSON_UNESCAPED_UNICODE) }}
                                        @else
                                            {{ $v }}
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-5">هیچ تۆمارێکی چاودێری نەدۆزرایەوە.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3 border-top">
        {{ $logs->links() }}
    </div>
</div>
@endsection
