<!doctype html><html lang="ku" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>{{ $title ?? 'سیستەمی بەڕێوەبردنی ئۆتۆمبێل' }}</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet"><style>body{font-family:Tahoma,Arial,sans-serif;background:#f5f7fb}.navbar{background:#123b5d}.card{border:0;box-shadow:0 4px 18px #123b5d12}.stat{font-size:30px;font-weight:700}.table td,.table th{vertical-align:middle}.mobile-action{min-height:54px;font-size:18px}.sidebar-link{display:block;padding:10px 14px;border-radius:8px;text-decoration:none;color:#334155}.sidebar-link:hover{background:#eef4f8}.badge-out{background:#fff0d5;color:#8a5a00}.badge-returned{background:#e5f7ed;color:#19734a}@media print{.no-print{display:none!important}body{background:#fff}.card{box-shadow:none}}</style></head><body><nav class="navbar navbar-dark mb-4 no-print"><div class="container"><a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">🚗 سیستەمی ئۆتۆمبێلەکان</a>@auth<div class="d-flex align-items-center gap-2 text-white"><div class="d-flex gap-2 me-3">@if(auth()->user()->isAdmin())<a class="text-white text-decoration-none" href="{{ route('drivers.index') }}">شۆفێرەکان</a><a class="text-white text-decoration-none" href="{{ route('vehicles.index') }}">ئۆتۆمبێلەکان</a><a class="text-white text-decoration-none" href="{{ route('reports.index') }}">راپۆرت</a>
@php($unread = auth()->user()->unreadNotifications)
<div class="dropdown">
<a class="text-white text-decoration-none position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">🔔@if($unread->count())<span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" style="font-size:10px">{{ $unread->count() }}</span>@endif</a>
<ul class="dropdown-menu dropdown-menu-end p-2" style="min-width:320px;max-height:400px;overflow:auto">
<li class="d-flex justify-content-between align-items-center px-2 pb-2">
<strong>ئاگادارکردنەوەکان</strong>
@if($unread->count())<form method="POST" action="{{ route('notifications.readAll') }}">@csrf<button class="btn btn-link btn-sm p-0">هەمووی وەک خوێندراوە</button></form>@endif
</li>
@forelse($unread->take(10) as $n)
<li>
<form method="POST" action="{{ route('notifications.read',$n->id) }}">
@csrf
<button type="submit" class="dropdown-item small text-wrap border-bottom py-2">
<div class="fw-bold">{{ $n->data['title'] ?? '' }}</div>
<div class="text-muted">{{ $n->data['body'] ?? '' }}</div>
</button>
</form>
</li>
@empty
<li><span class="dropdown-item-text text-muted small">هیچ ئاگادارکردنەوەیەکی نوێ نییە</span></li>
@endforelse
<li class="pt-2 px-2"><a class="small" href="{{ route('movements.index') }}#not-returned">بینینی لیستی نەگەڕاوەکان</a></li>
</ul>
</div>
@else<a class="text-white text-decoration-none" href="{{ route('driver.home') }}">گەشتەکانم</a><a class="text-white text-decoration-none" href="{{ route('driver.departure') }}">دەرچوون</a>@endif</div><span>{{ auth()->user()->name }}</span><form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-sm btn-outline-light">دەرچوون</button></form></div>@endauth</div></nav><main class="container pb-5">@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif @yield('content')</main><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script></body></html>
