@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm p-4">
            <div class="mb-4 pb-3 border-bottom">
                <h3 class="fw-bold mb-1">دەستکاری زانیاری بەکارهێنەر</h3>
                <p class="text-muted small mb-0">{{ $driver->name }} ({{ $driver->email }})</p>
            </div>
            @include('admin.drivers.form', [
                'action' => route('drivers.update', $driver),
                'method' => 'PUT',
                'driver' => $driver
            ])
        </div>
    </div>
</div>
@endsection
