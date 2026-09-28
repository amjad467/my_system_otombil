@extends('layouts.app') @section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2>📋 تۆماری چاودێری (Audit Log)</h2>
        <p class="text-muted">هەموو چالاکییە گرنگەکانی سیستەم</p>
    </div>
    <a href="{{ route('movements.index') }}" class="btn btn-outline-primary">گەڕانەوە بۆ جوڵەکان</a>
</div>

{{-- فلتەر --}}
<div class="card mb-4">
<div class="card-body">
<form method="GET" class="row g-2">
    <div class="col-md-4">
        <label class="form-label">جۆری چالاکی</label>
        <select name="action" class="form-select">
            <option value="">هەموو</option>
            @foreach($actions as $key => $label)
                <option value="{{ $key }}" @selected(request('action') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <button class="btn btn-primary">فلتەر</button>
        <a class="btn btn-light ms-2" href="{{ route('audit.index') }}">پاککردنەوە</a>
    </div>
</form>
</div>
</div>

<div class="card"><div class="card-body table-responsive">
<table class="table table-sm">
<thead><tr>
    <th>بەروار</th><th>بەکارهێنەر</th><th>چالاکی</th><th>وردەکاری</th>
</tr></thead>
<tbody>
@forelse($logs as $log)
<tr>
    <td class="text-nowrap">{{ $log->performed_at->format('Y-m-d H:i') }}</td>
    <td>{{ $log->user->name ?? '—' }}</td>
    <td>
        @php
            $badgeMap = [
                'departure_created' => 'bg-primary',
                'departure_overridden' => 'bg-danger',
                'return_recorded' => 'bg-success',
                'return_overridden' => 'bg-danger',
                'time_modified' => 'bg-warning text-dark',
                'backup_created' => 'bg-info',
            ];
        @endphp
        <span class="badge {{ $badgeMap[$log->action] ?? 'bg-secondary' }}">
            {{ $actions[$log->action] ?? $log->action }}
        </span>
    </td>
    <td>
        @if($log->details)
            <small class="text-muted">
                @if(isset($log->details['driver_id'])) شۆفێر: #{{ $log->details['driver_id'] }} @endif
                @if(isset($log->details['vehicle_id'])) | ئۆتۆمبێل: #{{ $log->details['vehicle_id'] }} @endif
                @if(isset($log->details['destination'])) | مەبەست: {{ $log->details['destination'] }} @endif
                @if(isset($log->details['override']) && $log->details['override']) | <span class="text-danger fw-bold">Override</span> @endif
                @if(isset($log->details['duration_minutes'])) | ماوە: {{ $log->details['duration_minutes'] }} خولەک @endif
            </small>
        @else
            —
        @endif
    </td>
</tr>
@empty
<tr><td colspan="4" class="text-center text-muted">هیچ تۆمارێک نییە.</td></tr>
@endforelse
</tbody>
</table>
{{ $logs->links() }}
</div></div>
@endsection
