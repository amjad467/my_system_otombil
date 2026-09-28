@extends('layouts.app') @section('content')
<div class="d-flex justify-content-between mb-3">
    <div>
        <h2>گەشتەکانی من</h2>
        <p class="text-muted">هەموو دەرچوون و گەڕانەوەکانت</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-primary" href="{{ route('driver.departure') }}">+ دەرچوونی نوێ</a>
        <a class="btn btn-outline-primary" href="{{ route('driver.active') }}">گەشتی کراوە</a>
    </div>
</div>
<div class="card"><div class="card-body table-responsive">
<table class="table">
<thead><tr>
    <th>ئۆتۆمبێل</th><th>جۆر</th><th>مەبەست</th><th>هۆکار</th><th>دەرچوون</th><th>گەڕانەوە</th><th>ماوە</th><th>دۆخ</th><th></th>
</tr></thead>
<tbody>
@forelse($movements as $m)
<tr>
    <td class="fw-bold">{{ $m->vehicle->number }}</td>
    <td>{{ $m->vehicle->type }}</td>
    <td>{{ $m->destination }}</td>
    <td>{{ $m->purpose ?: '—' }}</td>
    <td>{{ $m->departure_time->format('Y-m-d H:i') }}</td>
    <td>{{ $m->return_time?->format('Y-m-d H:i') ?? '—' }}</td>
    <td>
        @if($m->duration_minutes !== null)
            {{ intdiv($m->duration_minutes, 60) }} کاتژمێر {{ $m->duration_minutes % 60 }} خولەک
        @elseif($m->status === 'out')
            @php $live = \Carbon\Carbon::now()->diffInMinutes($m->departure_time); @endphp
            <span class="text-warning fw-bold">{{ intdiv($live, 60) }}:{{ str_pad($live % 60, 2, '0', STR_PAD_LEFT) }}</span>
        @else
            —
        @endif
    </td>
    <td>
        <span class="badge {{ $m->status==='out' ? 'badge-out' : 'badge-returned' }}">
            {{ $m->status==='out' ? 'لە دەرەوە' : 'گەڕاوەتەوە' }}
        </span>
    </td>
    <td>
        @if($m->status === 'out')
        <form method="POST" action="{{ route('driver.return', $m) }}">@csrf
        <button class="btn btn-sm btn-success">گەڕانەوە</button>
        </form>
        @endif
    </td>
</tr>
@empty
<tr><td colspan="9" class="text-center text-muted">هیچ گەشتێک نییە.</td></tr>
@endforelse
</tbody>
</table>
{{ $movements->links() }}
</div></div>
@endsection
