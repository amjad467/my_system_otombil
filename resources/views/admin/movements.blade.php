@extends('layouts.app') @section('content')
<div class="d-flex justify-content-between mb-3">
    <h2>هەموو جوڵەکان</h2>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="{{ route('audit.index') }}">📋 Audit Log</a>
        <a class="btn btn-outline-primary" href="{{ route('reports.index') }}">ڕاپۆرت</a>
    </div>
</div>

{{-- نەگەڕاوەکان --}}
<div id="not-returned" class="card mb-4 border-warning">
<div class="card-body">
<h5 class="text-warning-emphasis mb-3">🚧 ئۆتۆمبێلە نەگەڕاوەکان ({{ $notReturned->count() }})</h5>
@if($notReturned->isEmpty())
<p class="text-muted mb-0">هەموو ئۆتۆمبێلەکان گەڕاونەتەوە. ✅</p>
@else
<div class="table-responsive"><table class="table table-sm mb-0">
<thead><tr>
    <th>شۆفێر</th><th>ژمارەی ئۆتۆمبێل</th><th>جۆری ئۆتۆمبێل</th><th>مەبەست</th><th>کاتی دەرچوون</th><th>ماوەی ئێستا</th>
</tr></thead>
<tbody>
@foreach($notReturned as $m)
@php $live = \Carbon\Carbon::now()->diffInMinutes($m->departure_time); @endphp
<tr>
    <td>{{ $m->driver->name }}</td>
    <td class="fw-bold">{{ $m->vehicle->number }}</td>
    <td>{{ $m->vehicle->type }}</td>
    <td>{{ $m->destination }}</td>
    <td>{{ $m->departure_time->format('Y-m-d H:i') }}</td>
    <td><span class="badge bg-warning text-dark">{{ intdiv($live, 60) }} کاتژمێر {{ $live % 60 }} خولەک</span></td>
</tr>
@endforeach
</tbody>
</table></div>
@endif
</div>
</div>

{{-- هەموو جوڵەکان --}}
<div class="card"><div class="card-body table-responsive">
<table class="table">
<thead><tr>
    <th>شۆفێر</th><th>ئۆتۆمبێل</th><th>جۆر</th><th>مەبەست</th><th>هۆکار</th><th>دەرچوون</th><th>گەڕانەوە</th><th>ماوە</th><th>دۆخ</th>
</tr></thead>
<tbody>
@foreach($movements as $m)
<tr>
    <td>{{ $m->driver->name }}</td>
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
            <span class="text-warning">{{ intdiv($live, 60) }}:{{ str_pad($live % 60, 2, '0', STR_PAD_LEFT) }}</span>
        @else —
        @endif
    </td>
    <td>
        <span class="badge {{ $m->status==='out' ? 'badge-out' : 'badge-returned' }}">
            {{ $m->status==='out' ? 'لە دەرەوە' : 'گەڕاوەتەوە' }}
        </span>
        @if($m->override_by)
            <span class="badge bg-danger">Override</span>
        @endif
    </td>
</tr>
@endforeach
</tbody>
</table>
{{ $movements->links() }}
</div></div>
@endsection
