@extends('layouts.app') @section('content')
<div class="d-flex justify-content-between mb-3 align-items-center">
<h2>باکئەپی سیستەم</h2>
<form method="POST" action="{{ route('backups.create') }}">@csrf<button class="btn btn-primary">+ دروستکردنی باکئەپی نوێ</button></form>
</div>
<div class="card"><div class="card-body table-responsive">
<p class="text-muted small">باکئەپ وێنەیەکی تەواوی داتابەیسی سیستەمە لە کاتی دروستکردنیدا (شۆفێر، ئۆتۆمبێل، گەشتەکان). دەتوانیت داگریت و لە شوێنێکی سەلامەت هەڵیبگریت.</p>
<table class="table">
<thead><tr><th>ناوی فایل</th><th>قەبارە (KB)</th><th>بەروار</th><th></th></tr></thead>
<tbody>
@forelse($backups as $b)
<tr>
<td>{{ $b['name'] }}</td>
<td>{{ $b['size'] }}</td>
<td>{{ $b['date'] }}</td>
<td>
<a class="btn btn-sm btn-outline-success" href="{{ route('backups.download',$b['name']) }}">داگرتن</a>
<form class="d-inline" method="POST" action="{{ route('backups.destroy',$b['name']) }}" onsubmit="return confirm('ئایا دڵنیایت لە سڕینەوەی ئەم باکئەپە؟')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">سڕینەوە</button></form>
</td>
</tr>
@empty
<tr><td colspan="4" class="text-center text-muted py-4">هیچ باکئەپێک دروست نەکراوە. دوگمەی سەرەوە بکەرەوە بۆ دروستکردنی یەکەم باکئەپ.</td></tr>
@endforelse
</tbody>
</table>
</div></div>
@endsection
