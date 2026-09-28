<!-- Desktop Table View -->
<div class="responsive-table-desktop">
    <table class="table align-middle">
        <thead>
            <tr>
                <th>شۆفێر</th>
                <th>ئۆتۆمبێل</th>
                <th>مەبەست</th>
                <th>هۆکار</th>
                <th>دەرچوون</th>
                <th>گەڕانەوە</th>
                <th>ماوە</th>
                <th>دۆخ</th>
                <th class="text-end">کردار</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $m)
            <tr>
                <td>
                    <div class="fw-bold">{{ $m->driver?->name ?? '—' }}</div>
                    <small class="text-muted">{{ $m->driver?->phone ?: '—' }}</small>
                </td>
                <td>
                    <div class="fw-bold text-primary">{{ $m->vehicle?->number ?? '—' }}</div>
                    <small class="text-muted">{{ $m->vehicle?->type ?? '—' }}</small>
                </td>
                <td>
                    <div class="fw-semibold">{{ $m->destination }}</div>
                </td>
                <td>
                    <small class="text-muted">{{ $m->purpose ?: '—' }}</small>
                </td>
                <td>
                    <div>{{ $m->departure_time->format('H:i') }}</div>
                    <small class="text-muted">{{ $m->departure_time->format('Y-m-d') }}</small>
                </td>
                <td>
                    @if($m->return_time)
                        <div>{{ $m->return_time->format('H:i') }}</div>
                        <small class="text-muted">{{ $m->return_time->format('Y-m-d') }}</small>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    @if($m->duration_minutes !== null)
                        <span class="fw-semibold">{{ intdiv($m->duration_minutes, 60) }} کاتژمێر {{ $m->duration_minutes % 60 }} خولەک</span>
                    @elseif($m->status === 'out')
                        <span class="badge bg-warning text-dark live-timer" data-start="{{ $m->departure_time->toISOString() }}">
                            {{ intdiv($m->current_duration_minutes, 60) }} کاتژمێر {{ $m->current_duration_minutes % 60 }} خولەک
                        </span>
                    @else
                        —
                    @endif
                </td>
                <td>
                    <span class="badge {{ $m->status === 'out' ? 'badge-out' : 'badge-returned' }}">
                        {{ $m->status === 'out' ? 'لە دەرەوە' : 'گەڕاوەتەوە' }}
                    </span>
                    @if($m->override_by)
                        <span class="badge bg-danger" title="بە تێپەڕاندن (Override) تۆمار کراوە لەلایەن {{ $m->overrideUser?->name }}">Override</span>
                    @endif
                </td>
                <td class="text-end">
                    @if($m->status === 'out')
                        <form method="POST" action="{{ route('driver.return', $m) }}" class="d-inline" onsubmit="return confirm('ئایا دڵنیایت لە گەڕاندنەوەی ئەم ئۆتۆمبێلە؟')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="bi bi-check2"></i> گەڕانەوە
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-2 opacity-50 d-block mb-2"></i>
                    هیچ جوڵەیەک بە پێی ئەم فلتەرە نەدۆزرایەوە.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Mobile Card View -->
<div class="p-3 d-lg-none">
    <div class="row g-3">
        @forelse($movements as $m)
        <div class="col-12 mobile-card-item">
            <div class="card p-3 border shadow-none bg-body-tertiary">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="fw-bold mb-0 text-primary">{{ $m->vehicle?->number }}</h6>
                        <small class="text-muted">{{ $m->vehicle?->type }}</small>
                    </div>
                    <span class="badge {{ $m->status === 'out' ? 'badge-out' : 'badge-returned' }}">
                        {{ $m->status === 'out' ? 'لە دەرەوە' : 'گەڕاوەتەوە' }}
                    </span>
                </div>

                <div class="bg-body p-2 rounded-3 mb-2 small">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">شۆفێر:</span>
                        <span class="fw-bold">{{ $m->driver?->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">مەبەست:</span>
                        <span>{{ $m->destination }}</span>
                    </div>
                    @if($m->purpose)
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">هۆکار:</span>
                        <span>{{ $m->purpose }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">دەرچوون:</span>
                        <span>{{ $m->departure_time->format('Y-m-d H:i') }}</span>
                    </div>
                    @if($m->return_time)
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">گەڕانەوە:</span>
                        <span>{{ $m->return_time->format('Y-m-d H:i') }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">ماوە:</span>
                        <span class="fw-semibold">
                            @if($m->duration_minutes !== null)
                                {{ intdiv($m->duration_minutes, 60) }} کاتژمێر {{ $m->duration_minutes % 60 }} خولەک
                            @elseif($m->status === 'out')
                                <span class="text-warning fw-bold live-timer" data-start="{{ $m->departure_time->toISOString() }}">
                                    {{ intdiv($m->current_duration_minutes, 60) }} کاتژمێر {{ $m->current_duration_minutes % 60 }} خولەک
                                </span>
                            @else — @endif
                        </span>
                    </div>
                </div>

                @if($m->status === 'out')
                    <form method="POST" action="{{ route('driver.return', $m) }}" onsubmit="return confirm('تۆمارکردنی گەڕانەوە بۆ {{ $m->vehicle?->number }}؟')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success w-100">
                            <i class="bi bi-check-circle me-1"></i> تۆمارکردنی گەڕانەوە
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @empty
        <div class="col-12 text-center text-muted py-4">هیچ جوڵەیەک بە پێی ئەم فلتەرە نییە.</div>
        @endforelse
    </div>
</div>

<div class="p-3 border-top">
    {{ $movements->links() }}
</div>
