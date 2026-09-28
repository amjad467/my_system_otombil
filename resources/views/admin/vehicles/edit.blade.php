@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm p-4">
            <div class="mb-4 pb-3 border-bottom">
                <h3 class="fw-bold mb-1">دەستکاری زانیاری ئۆتۆمبێل</h3>
                <p class="text-muted small mb-0">{{ $vehicle->number }} — {{ $vehicle->type }} ({{ $vehicle->model }})</p>
            </div>
            @include('admin.vehicles.form', [
                'action' => route('vehicles.update', $vehicle),
                'method' => 'PUT',
                'vehicle' => $vehicle
            ])
        </div>
    </div>
</div>
@endsection
