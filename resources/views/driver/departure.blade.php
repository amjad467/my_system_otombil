@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm p-4">
            <div class="mb-4 pb-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="fw-bold mb-1">🚗 تۆمارکردنی دەرچوونی ئۆتۆمبێل</h3>
                    <p class="text-muted small mb-0">کاتی دەرچوون بە شێوەی خۆکار لە لایەن سیستەمەوە تۆمار دەکرێت</p>
                </div>
                <span class="fs-1 text-primary"><i class="bi bi-geo-alt"></i></span>
            </div>

            @if(!$isAdmin && $hasOpenTrip)
                <div class="alert alert-warning border-2 rounded-4 p-4 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-exclamation-circle-fill fs-1 text-warning"></i>
                        <div>
                            <h5 class="fw-bold mb-1">گەشتێکی کراوەت هەیە!</h5>
                            <p class="small mb-2">تۆ لە ئێستادا گەشتێکی کراوەت هەیە و ناتوانیت دەرچوونی نوێ تۆمار بکەیت تا ئەو کاتەی گەڕانەوەی ئۆتۆمبێلی پێشوو تۆمار دەکەیت.</p>
                            <a href="{{ route('driver.active') }}" class="btn btn-warning fw-bold">
                                <i class="bi bi-arrow-left-circle me-1"></i> چوون بۆ گەشتی ئێستام و گەڕانەوە
                            </a>
                        </div>
                    </div>
                </div>
            @elseif($vehicles->isEmpty())
                <div class="alert alert-info rounded-4 p-4 text-center">
                    <i class="bi bi-info-circle fs-1 d-block mb-2"></i>
                    <h5 class="fw-bold">هیچ ئۆتۆمبێلێکی بەردەست نییە!</h5>
                    <p class="small text-muted mb-0">هەموو ئۆتۆمبێلەکان لە دەرەوەن یان لە دۆخی چاککردنەوەدان.</p>
                </div>
            @else
                <form method="POST" action="{{ route('driver.departure.store') }}" id="departureForm">
                    @csrf

                    {{-- Admin: Driver Selection & Override --}}
                    @if($isAdmin)
                    <div class="p-3 bg-body-tertiary rounded-4 mb-4 border">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-badge me-1"></i> هەڵبژاردنی شۆفێر (تایبەت بە بەڕێوەبەر)</h6>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">شۆفێری گەشت <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg @error('user_id') is-invalid @enderror" name="user_id" id="driverSelect" required>
                                <option value="">شۆفێرێک هەڵبژێرە...</option>
                                @foreach($drivers as $d)
                                    <option value="{{ $d->id }}" data-status="{{ $d->trip_status }}" @selected(old('user_id') == $d->id)>
                                        {{ $d->name }} 
                                        @if($d->trip_status === 'لە دەرەوەیە')
                                            — ⚠️ لە دەرەوەیە (پێویستی بە Override هەیە)
                                        @else
                                            — ✅ بەردەست
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="override" value="1" id="overrideCheck">
                            <label class="form-check-label fw-semibold" for="overrideCheck">
                                <span class="text-danger fw-bold">تێپەڕاندنی ڕێساکان (Override):</span>
                                ڕێگەدان بە دەرچوون تەنانەت ئەگەر شۆفێر یان ئۆتۆمبێل لە دەرەوە بێت
                            </label>
                            <div class="small text-muted">ئەم کردارە لە ناو تۆماری چاودێری (Audit Log) تۆمار دەکرێت.</div>
                        </div>
                    </div>
                    @endif

                    {{-- Vehicle Selection --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ژمارەی ئۆتۆمبێل <span class="text-danger">*</span></label>
                        <select class="form-select form-select-lg @error('vehicle_id') is-invalid @enderror" id="vehicle_select" name="vehicle_id" required>
                            <option value="">ئۆتۆمبێلێک هەڵبژێرە...</option>
                            @foreach($vehicles as $v)
                                @php 
                                    $isOut = in_array($v->id, $outVehicleIds); 
                                    $isMaint = $v->status === 'maintenance';
                                @endphp
                                <option value="{{ $v->id }}"
                                        data-type="{{ $v->type }}"
                                        data-model="{{ $v->model }}"
                                        data-status="{{ $isOut ? 'out' : ($isMaint ? 'maintenance' : 'available') }}"
                                        @selected(old('vehicle_id') == $v->id)>
                                    {{ $v->number }} ({{ $v->type }})
                                    @if($isOut)
                                        — ⚠️ لە دەرەوەیە
                                    @elseif($isMaint)
                                        — 🔧 لە چاککردنەوەدایە
                                    @else
                                        — ✅ بەردەست
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Vehicle Info Auto-Display --}}
                    <div class="mb-3">
                        <label class="form-label small text-muted">جۆر و مۆدێلی دیاریکراو</label>
                        <input type="text" class="form-control bg-body-tertiary" id="vehicle_info_display" readonly placeholder="خۆکار دیاری دەکرێت...">
                    </div>

                    {{-- Destination --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">مەبەست / شوێنی چوون <span class="text-danger">*</span></label>
                        <input class="form-control form-control-lg @error('destination') is-invalid @enderror" name="destination" required placeholder="وەک: بەغدا، هەولێر، پشکنینی گومرگ..." value="{{ old('destination') }}">
                        @error('destination')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Purpose --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">هۆکاری گەشت</label>
                        <textarea class="form-control @error('purpose') is-invalid @enderror" name="purpose" rows="2" placeholder="بۆچی دەچیت؟ ئەرکی کار...">{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="mb-4">
                        <label class="form-label small text-muted">تێبینی زیاتر (ئارەزوومەندانە)</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="کێ لەگەڵت دەڕوات؟ کەلوپەل یان هەر زانیارییەکی تر...">{{ old('notes') }}</textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg py-3 shadow">
                            <i class="bi bi-send-check me-1"></i> تۆمارکردنی دەرچوون ئێستا
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    const vehicleSelect = document.getElementById('vehicle_select');
    const vehicleDisplay = document.getElementById('vehicle_info_display');

    if (vehicleSelect && vehicleDisplay) {
        function updateVehicleInfo() {
            const opt = vehicleSelect.options[vehicleSelect.selectedIndex];
            if (opt && opt.value) {
                const type = opt.getAttribute('data-type') || '';
                const model = opt.getAttribute('data-model') || '';
                vehicleDisplay.value = type + (model ? ' — ' + model : '');
            } else {
                vehicleDisplay.value = '';
            }
        }

        vehicleSelect.addEventListener('change', updateVehicleInfo);
        updateVehicleInfo();
    }
</script>
@endpush
@endsection
