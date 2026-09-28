@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        @if($movement)
            <div class="card border-0 shadow-lg p-4 text-center">
                <div class="mb-3">
                    <span class="rounded-circle bg-warning bg-opacity-15 text-warning p-3 d-inline-flex fs-1 mb-2">
                        🚗
                    </span>
                    <span class="badge bg-warning text-dark d-block mx-auto mb-2" style="width: fit-content;">گەشت لە دەرەوەیە</span>
                    <h3 class="fw-bold mb-1">{{ $movement->vehicle?->number }}</h3>
                    <p class="text-muted">{{ $movement->vehicle?->type }} {{ $movement->vehicle?->model ? '('.$movement->vehicle?->model.')' : '' }}</p>
                </div>

                <div class="p-3 bg-body-tertiary rounded-4 text-start mb-4">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">مەبەست:</span>
                        <span class="fw-bold">{{ $movement->destination }}</span>
                    </div>
                    @if($movement->purpose)
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">هۆکار:</span>
                        <span>{{ $movement->purpose }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">کاتی دەرچوون:</span>
                        <span>{{ $movement->departure_time->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 align-items-center">
                        <span class="text-muted">ماوەی لە دەرەوە بوون:</span>
                        <span class="fs-5 fw-bold text-primary" id="activeLiveDuration">
                            {{ intdiv($movement->current_duration_minutes, 60) }} کاتژمێر {{ $movement->current_duration_minutes % 60 }} خولەک
                        </span>
                    </div>
                </div>

                <form method="POST" action="{{ route('driver.return', $movement) }}" onsubmit="return confirm('ئایا دڵنیایت لە تۆمارکردنی گەڕانەوەی ئەم ئۆتۆمبێلە؟')">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg w-100 py-3 shadow">
                        <i class="bi bi-check2-circle fs-4 me-1"></i> تۆمارکردنی گەڕانەوەی ئۆتۆمبێل
                    </button>
                </form>
            </div>
        @else
            <div class="card border-0 shadow-sm p-5 text-center">
                <i class="bi bi-geo-alt-fill text-muted opacity-50 fs-1 d-block mb-3"></i>
                <h4 class="fw-bold">هیچ گەشتێکی کراوەت نییە</h4>
                <p class="text-muted small mb-4">لە ئێستادا هیچ دەرچوونێکی تۆمارکراوت نییە کە نەگەڕابێتەوە.</p>
                <a class="btn btn-primary btn-lg" href="{{ route('driver.departure') }}">
                    <i class="bi bi-plus-circle me-1"></i> تۆمارکردنی دەرچوونی نوێ
                </a>
            </div>
        @endif
    </div>
</div>

@if($movement)
@push('scripts')
<script>
    // Live Timer for active view
    const startTime = new Date('{{ $movement->departure_time->toISOString() }}');
    const timerEl = document.getElementById('activeLiveDuration');

    function updateActiveDuration() {
        const now = new Date();
        const diff = Math.floor((now - startTime) / 60000);
        const h = Math.floor(diff / 60);
        const m = diff % 60;
        if (timerEl) {
            timerEl.textContent = h + ' کاتژمێر ' + m + ' خولەک';
        }
    }

    setInterval(updateActiveDuration, 10000);
</script>
@endpush
@endif
@endsection
