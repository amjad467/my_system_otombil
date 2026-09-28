@extends('layouts.app')

@section('content')
<div class="row justify-content-center py-5">
    <div class="col-12 col-md-6 text-center">
        <div class="p-5 card border-0 shadow-sm rounded-4">
            <span class="display-1 fw-bold text-primary mb-3">404</span>
            <h3 class="fw-bold mb-2">پەڕەکە نەدۆزرایەوە!</h3>
            <p class="text-muted mb-4">ئەو ناونیشانەی داوات کردووە بوونی نییە یان سڕدراوەتەوە.</p>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-primary px-4 py-2">
                    <i class="bi bi-house-door me-1"></i> گەڕانەوە بۆ داشبۆرد
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
