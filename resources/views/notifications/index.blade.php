@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">ئاگادارکردنەوەکان</h2>
        <p class="text-muted mb-0 small">هەموو ئاگادارییەکانی دەرچوون، گەڕانەوە، دواکەوتن و ئۆتۆمبێلەکان</p>
    </div>
    <div class="d-flex gap-2">
        <form method="POST" action="{{ route('notifications.readAll') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-outline-primary d-flex align-items-center gap-2">
                <i class="bi bi-check2-all"></i>
                <span>خوێندنەوەی هەمووی</span>
            </button>
        </form>
    </div>
</div>

<!-- Tabs / Filters -->
<div class="d-flex gap-2 mb-4">
    <a href="{{ route('notifications.index') }}" class="btn btn-sm {{ !request('filter') ? 'btn-primary' : 'btn-light' }}">
        هەمووی
    </a>
    <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="btn btn-sm {{ request('filter') === 'unread' ? 'btn-primary' : 'btn-light' }}">
        نەخوێندراوەکان
    </a>
    <a href="{{ route('notifications.index', ['filter' => 'read']) }}" class="btn btn-sm {{ request('filter') === 'read' ? 'btn-primary' : 'btn-light' }}">
        خوێندراوەکان
    </a>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="list-group list-group-flush">
        @forelse($notifications as $n)
            @php
                $type = $n->data['event'] ?? 'default';
                $iconClass = match($type) {
                    'departed' => 'bi-send-fill text-primary bg-primary bg-opacity-10',
                    'returned' => 'bi-check-circle-fill text-success bg-success bg-opacity-10',
                    'overdue'  => 'bi-exclamation-triangle-fill text-danger bg-danger bg-opacity-10',
                    'maintenance' => 'bi-wrench text-warning bg-warning bg-opacity-10',
                    default => 'bi-info-circle-fill text-info bg-info bg-opacity-10'
                };
            @endphp
            <div class="list-group-item p-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 {{ is_null($n->read_at) ? 'bg-primary bg-opacity-10 border-start border-primary border-4' : '' }}">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center fs-4 flex-shrink-0 {{ $iconClass }}" style="width: 44px; height: 44px;">
                        <i class="bi {{ strtok($iconClass, ' ') }}"></i>
                    </div>
                    <div>
                        <div class="fw-bold mb-1 fs-6 text-body">
                            {{ $n->data['title'] ?? 'ئاگادارکردنەوە' }}
                            @if(is_null($n->read_at))
                                <span class="badge bg-danger ms-1" style="font-size: 10px;">نوێ</span>
                            @endif
                        </div>
                        <p class="mb-1 text-muted">{{ $n->data['body'] ?? '' }}</p>
                        <small class="text-secondary opacity-75"><i class="bi bi-clock me-1"></i>{{ $n->created_at->diffForHumans() }} ({{ $n->created_at->format('Y-m-d H:i') }})</small>
                    </div>
                </div>

                <div class="d-flex gap-2 w-100 w-md-auto justify-content-end">
                    @if(is_null($n->read_at))
                        <form method="POST" action="{{ route('notifications.read', $n->id) }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary" title="دیاریکردن وەک خوێندراوە">
                                <i class="bi bi-check2"></i> خوێندراوە
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('notifications.destroy', $n->id) }}" class="m-0" onsubmit="return confirm('سڕینەوەی ئەم ئاگادارییە؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="سڕینەوە">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-5 text-center text-muted">
                <i class="bi bi-bell-slash fs-1 opacity-25 d-block mb-2"></i>
                <h5 class="fw-bold">هیچ ئاگادارکردنەوەیەک نییە</h5>
                <p class="small text-muted mb-0">لێرەدا ئاگادارکردنەوە نوێیەکان پیشان دەدرێن کاتێک ئۆتۆمبێلەکان دەردەچن یان دەگەڕێنەوە.</p>
            </div>
        @endforelse
    </div>

    <div class="p-3 border-top">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
