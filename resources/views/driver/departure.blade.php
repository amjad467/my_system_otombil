@extends('layouts.app') @section('content')
<div class="row justify-content-center"><div class="col-lg-8"><div class="card"><div class="card-body p-4">
<h2>🚗 تۆمارکردنی دەرچوون</h2>
<p class="text-muted">کاتی دەرچوون بە شێوەی خۆکار تۆمار دەکرێت.</p>

@if(!$isAdmin && $hasOpenTrip)
    <div class="alert alert-danger">
        <strong>⚠ ئەم شۆفێرە هێشتا نەگەڕاوەتەوە و ناتوانێت دەرچوونی نوێ تۆمار بکات.</strong>
        <br>تکایە سەرەتا گەشتی کراوەکەت تەواو بکە.
        <a href="{{ route('driver.active') }}" class="btn btn-sm btn-warning mt-2">بینینی گەشتی کراوە</a>
    </div>
@elseif($vehicles->isEmpty())
    <div class="alert alert-warning">هیچ ئۆتۆمبێلێکی بەردەست نییە.</div>
@else
<form method="POST" action="{{ route('driver.departure.store') }}">@csrf

{{-- Admin: هەڵبژاردنی شۆفێر --}}
@if($isAdmin)
<div class="mb-3">
    <label class="form-label fw-bold">شۆفێر</label>
    <select class="form-select form-select-lg" name="user_id" required>
        <option value="">هەڵبژێرە...</option>
        @foreach($drivers as $d)
            <option value="{{ $d->id }}"
                {{ $d->trip_status === 'لە دەرەوەیە' ? 'class=text-danger' : '' }}>
                {{ $d->name }}
                @if($d->trip_status === 'لە دەرەوەیە')
                    — ⚠ لە دەرەوەیە
                @else
                    — ✅ بەردەست
                @endif
            </option>
        @endforeach
    </select>
    <div class="form-text">شۆفێرانی (⚠) هێشتا نەگەڕاونەتەوە. بۆ هەڵبژاردنیان Override پێویستە.</div>
</div>
<div class="mb-3 form-check">
    <input type="checkbox" class="form-check-input" name="override" value="1" id="overrideCheck">
    <label class="form-check-label" for="overrideCheck">Override — تێپەڕاندنی ڕێسا (بۆ ئەدمین)</label>
</div>
@endif

{{-- هەڵبژاردنی ئۆتۆمبێل --}}
<div class="mb-3">
    <label class="form-label fw-bold">ژمارەی ئۆتۆمبێل</label>
    <select class="form-select form-select-lg" id="vehicle_select" name="vehicle_id" required>
        <option value="">هەڵبژێرە...</option>
        @foreach($vehicles as $v)
            @php $isOut = in_array($v->id, $outVehicleIds); @endphp
            <option value="{{ $v->id }}"
                    data-type="{{ $v->type }}"
                    data-model="{{ $v->model }}"
                    {{ $isOut ? 'class=text-danger' : '' }}>
                {{ $v->number }}
                @if($isOut)
                    — ⚠ لە دەرەوەیە
                @else
                    — ✅ بەردەست
                @endif
            </option>
        @endforeach
    </select>
</div>

{{-- جۆری ئۆتۆمبێل (خۆکار) --}}
<div class="mb-3">
    <label class="form-label">جۆری ئۆتۆمبێل</label>
    <input type="text" class="form-control form-control-lg" id="vehicle_type_display" readonly placeholder="بەدوای هەڵبژاردنی ژمارە خۆکار پڕدەبێتەوە">
</div>

<div class="mb-3">
    <label class="form-label fw-bold">بۆ کوێ دەچیت؟</label>
    <input class="form-control form-control-lg" name="destination" required placeholder="وەک: بەغدا، گومرگ، ناوچەی ..." value="{{ old('destination') }}">
</div>

<div class="mb-3">
    <label class="form-label">هۆکاری گەشت</label>
    <textarea class="form-control" name="purpose" rows="3" placeholder="هۆکاری چوون...">{{ old('purpose') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">تێبینی</label>
    <textarea class="form-control" name="notes" rows="2">{{ old('notes') }}</textarea>
</div>

<button class="btn btn-primary w-100 mobile-action">تۆمارکردنی دەرچوون</button>
</form>

<script>
document.getElementById('vehicle_select').addEventListener('change', function () {
    var opt = this.options[this.selectedIndex];
    var type = opt.getAttribute('data-type') || '';
    var model = opt.getAttribute('data-model') || '';
    document.getElementById('vehicle_type_display').value = opt.value ? (type + (model ? ' — ' + model : '')) : '';
});
</script>
@endif
</div></div></div></div>
@endsection
