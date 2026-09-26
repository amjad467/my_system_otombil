@extends('layouts.app') @section('content')<div class="d-flex justify-content-between mb-3"><h2>هەموو جوڵەکان</h2><a class="btn btn-outline-primary" href="{{ route('reports.index') }}">راپۆرت</a></div>
<div id="not-returned" class="card mb-4 border-warning">
<div class="card-body">
<h5 class="text-warning-emphasis mb-3">🚧 ئۆتۆمبێلە نەگەڕاوەکان ({{ $notReturned->count() }})</h5>
@if($notReturned->isEmpty())
<p class="text-muted mb-0">هەموو ئۆتۆمبێلەکان گەڕاونەتەوە.</p>
@else
<div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>ئۆتۆمبێل</th><th>جۆر</th><th>شۆفێر</th><th>مەبەست</th><th>کاتی دەرچوون</th></tr></thead><tbody>
@foreach($notReturned as $m)
<tr><td>{{ $m->vehicle->number }}</td><td>{{ $m->vehicle->type }}</td><td>{{ $m->driver->name }}</td><td>{{ $m->destination }}</td><td>{{ $m->departure_time->format('Y-m-d H:i') }}</td></tr>
@endforeach
</tbody></table></div>
@endif
</div>
</div>
<div class="card"><div class="card-body table-responsive"><table class="table"><thead><tr><th>شۆفێر</th><th>ئۆتۆمبێل</th><th>مەبەست</th><th>هۆکار</th><th>دەرچوون</th><th>گەڕانەوە</th><th>دۆخ</th></tr></thead><tbody>@foreach($movements as $m)<tr><td>{{ $m->driver->name }}</td><td>{{ $m->vehicle->number }}</td><td>{{ $m->destination }}</td><td>{{ $m->purpose ?: '—' }}</td><td>{{ $m->departure_time->format('Y-m-d H:i') }}</td><td>{{ $m->return_time?->format('Y-m-d H:i') ?? '—' }}</td><td>{{ $m->status==='out'?'لە دەرەوە':'گەڕاوەتەوە' }}</td></tr>@endforeach</tbody></table>{{ $movements->links() }}</div></div>@endsection
