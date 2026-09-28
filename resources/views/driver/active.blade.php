@extends('layouts.app') @section('content')
<div class="card"><div class="card-body p-4 text-center">
@if($movement)
<h2>🚗 ئۆتۆمبێلەکە لە دەرەوەیە</h2>
<p class="fs-5 fw-bold">{{ $movement->vehicle->number }} — {{ $movement->vehicle->type }}</p>
<p class="text-muted">مەبەست: {{ $movement->destination }}</p>
@if($movement->purpose)
<p class="text-muted">هۆکار: {{ $movement->purpose }}</p>
@endif
<p class="text-muted">دەرچوون: {{ $movement->departure_time->format('Y-m-d H:i') }}</p>

{{-- ماوەی ئێستا --}}
@php
    $liveMinutes = \Carbon\Carbon::now()->diffInMinutes($movement->departure_time);
@endphp
<div class="my-3">
    <span class="badge bg-warning text-dark fs-5 p-2" id="liveDuration">
        ⏱ {{ intdiv($liveMinutes, 60) }} کاتژمێر {{ $liveMinutes % 60 }} خولەک
    </span>
</div>

<form method="POST" action="{{ route('driver.return', $movement) }}">@csrf
<button class="btn btn-success btn-lg px-5">✓ تۆمارکردنی گەڕانەوە</button>
</form>

<script>
// نوێکردنەوەی خۆکاری ماوە هەر ٣٠ چرکە
setInterval(function(){
    var start = new Date('{{ $movement->departure_time->toISOString() }}');
    var now = new Date();
    var diff = Math.floor((now - start) / 60000);
    var h = Math.floor(diff / 60);
    var m = diff % 60;
    document.getElementById('liveDuration').innerHTML = '⏱ ' + h + ' کاتژمێر ' + m + ' خولەک';
}, 30000);
</script>

@else
<h2>هیچ گەشتێکی کراوە نییە</h2>
<p class="text-muted mb-3">ئێستا هیچ دەرچوونێکی کراوەت نییە.</p>
<a class="btn btn-primary" href="{{ route('driver.departure') }}">تۆمارکردنی دەرچوون</a>
@endif
</div></div>
@endsection
