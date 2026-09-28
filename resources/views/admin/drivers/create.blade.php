@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm p-4">
            <div class="mb-4 pb-3 border-bottom">
                <h3 class="fw-bold mb-1">زیادکردنی بەکارهێنەری نوێ</h3>
                <p class="text-muted small mb-0">تۆمارکردنی شۆفێر یان بەڕێوەبەری نوێ لە سیستەمدا</p>
            </div>
            @include('admin.drivers.form', [
                'action' => route('drivers.store'),
                'method' => 'POST',
                'driver' => null
            ])
        </div>
    </div>
</div>
@endsection
