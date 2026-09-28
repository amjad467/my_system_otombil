@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm p-4">
            <div class="mb-4 pb-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="fw-bold mb-1">⚙️ ڕێکخستنەکانی سیستەم</h3>
                    <p class="text-muted small mb-0">دیاریکردنی سنوری کاتی دواکەوتن و ڕێکخستنە گشتییەکان</p>
                </div>
                <span class="fs-1 text-primary"><i class="bi bi-sliders"></i></span>
            </div>

            <form method="POST" action="{{ route('settings.update') }}">
                @csrf

                <!-- General Organization Info -->
                <div class="mb-4">
                    <h5 class="fw-bold text-primary mb-3"><i class="bi bi-building me-1"></i> زانیاری گشتی</h5>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ناوی دامەزراوە / سیستەم <span class="text-danger">*</span></label>
                        <input class="form-control form-control-lg @error('org_name') is-invalid @enderror" name="org_name" value="{{ old('org_name', $settings['org_name']) }}" required>
                        @error('org_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <!-- Live Not Returned Thresholds -->
                <div class="mb-4">
                    <h5 class="fw-bold text-primary mb-2"><i class="bi bi-stopwatch me-1"></i> سنوورەکانی کاتی دەرچوون و نەگەڕاوە</h5>
                    <p class="text-muted small mb-3">ئەم سنوورانە بۆ پیشاندانی دۆخی ڕەنگی ئاگادارکردنەوەی گەشتە کراوەکان لە داشبۆرد و جوڵەکان بەکاردێن:</p>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-warning">
                                <i class="bi bi-exclamation-circle me-1"></i> کاتی سەرنج (Attention Hours)
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.5" min="0.5" max="24" class="form-control form-control-lg @error('attention_hours') is-invalid @enderror" name="attention_hours" value="{{ old('attention_hours', $settings['attention_hours']) }}" required>
                                <span class="input-group-text">کاتژمێر</span>
                            </div>
                            <small class="text-muted">گەشتەکانی نێوان ئەم کاتە و کاتی مەترسی بە ڕەنگی زەرد پیشان دەدرێن (بۆ نموونە: ٢ کاتژمێر).</small>
                            @error('attention_hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-danger">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> کاتی مەترسی (Warning Hours)
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.5" min="1" max="72" class="form-control form-control-lg @error('warning_hours') is-invalid @enderror" name="warning_hours" value="{{ old('warning_hours', $settings['warning_hours']) }}" required>
                                <span class="input-group-text">کاتژمێر</span>
                            </div>
                            <small class="text-muted">ئەگەر ئۆتۆمبێل زیاتر لەم ماوەیە بمێنێتەوە بە سووری پیشان دەدرێت و ئاگاداری دروست دەبێت (بۆ نموونە: ٤ کاتژمێر).</small>
                            @error('warning_hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Appearance Preference -->
                <div class="mb-4">
                    <h5 class="fw-bold text-primary mb-3"><i class="bi bi-palette me-1"></i> ڕووکەش و دۆخی تاریک</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="dark_mode" value="1" id="darkModeSetting" @checked(old('dark_mode', $settings['dark_mode']) == '1')>
                        <label class="form-check-label fw-semibold" for="darkModeSetting">دۆخی تاریک (Dark Mode) وەک دۆخی بنەڕەتی سیستەم دیاری بکرێت</label>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-check-circle me-1"></i> پاشەکەوتکردنی ڕێکخستنەکان
                    </button>
                    <a class="btn btn-light btn-lg px-4" href="{{ route('dashboard') }}">
                        گەڕانەوە
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
