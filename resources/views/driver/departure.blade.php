@extends('layouts.app') @section('content')<div class="row justify-content-center"><div class="col-lg-7"><div class="card"><div class="card-body p-4"><h2>🚗 تۆمارکردنی دەرچوون</h2><p class="text-muted">کاتی دەرچوون بە شێوەی خۆکار تۆمار دەکرێت.</p>@if($vehicles->isEmpty())<div class="alert alert-warning">هیچ ئۆتۆمبێلێکی بەردەست نییە.</div>@else<form method="POST" action="{{ route('driver.departure.store') }}">@csrf<div class="mb-3"><label>ژمارەی ئۆتۆمبێل</label><select class="form-select form-select-lg" id="vehicle_select" name="vehicle_id" required><option value="">هەڵبژێرە...</option>@foreach($vehicles as $v)<option value="{{ $v->id }}" data-type="{{ $v->type }}" data-model="{{ $v->model }}">{{ $v->number }}</option>@endforeach</select></div><div class="mb-3"><label>جۆری ئۆتۆمبێل</label><input type="text" class="form-control form-control-lg" id="vehicle_type_display" readonly placeholder="بەدوای هەڵبژاردنی ژمارە خۆکار پڕدەبێتەوە"></div><div class="mb-3"><label>بۆ کوێ دەچیت؟</label><input class="form-control form-control-lg" name="destination" required placeholder="وەک: بەغدا، گومرگ، ناوچەی ..."></div><div class="mb-3"><label>هۆکاری گەشت</label><textarea class="form-control" name="purpose" rows="3" placeholder="هۆکاری چوون..."></textarea></div><div class="mb-3"><label>تێبینی</label><textarea class="form-control" name="notes" rows="2"></textarea></div><button class="btn btn-primary w-100 mobile-action">تۆمارکردنی دەرچوون</button></form>
<script>
document.getElementById('vehicle_select').addEventListener('change', function () {
    var opt = this.options[this.selectedIndex];
    var type = opt.getAttribute('data-type') || '';
    var model = opt.getAttribute('data-model') || '';
    document.getElementById('vehicle_type_display').value = opt.value ? (type + (model ? ' — ' + model : '')) : '';
});
</script>
@endif</div></div></div></div>@endsection
