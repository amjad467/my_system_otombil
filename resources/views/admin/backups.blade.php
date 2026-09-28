@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">باکئەپ و پاراستنی زانیارییەکان</h2>
        <p class="text-muted mb-0 small">وەرگرتنی کۆپیی پارێزراو لە دەیتابەیس و گەڕاندنەوە لە کاتی پێویستدا</p>
    </div>
    <form method="POST" action="{{ route('backups.create') }}" class="m-0">
        @csrf
        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-cloud-arrow-up-fill"></i>
            <span>دروستکردنی باکئەپی نوێ</span>
        </button>
    </form>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-3">
            <span class="fs-1 text-primary"><i class="bi bi-shield-lock-fill"></i></span>
            <div>
                <h5 class="fw-bold mb-1">پاراستنی داتاکان</h5>
                <p class="text-muted small mb-0">
                    باکئەپ وێنەیەکی تەواوی دەیتابەیسی ئێستای سیستەمە (شۆفێرەکان، ئۆتۆمبێلەکان، دەرچوون و گەڕانەوەکان و تۆماری چاودێری). پێش هەر گەڕاندنەوەیەک کۆپییەکی خۆکار بۆ پاراستن دروست دەکرێت.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-header bg-transparent p-3">
        <h5 class="fw-bold mb-0">مێژووی باکئەپەکان</h5>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>ناوی فایل</th>
                    <th>قەبارە</th>
                    <th>بەروار و کات</th>
                    <th>دروستکراوە لەلایەن</th>
                    <th class="text-end">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backups as $b)
                <tr>
                    <td>
                        <div class="fw-bold font-monospace text-primary">
                            <i class="bi bi-file-earmark-zip me-1"></i>{{ $b['name'] }}
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-secondary bg-opacity-10 text-body">{{ $b['size'] }} KB</span>
                    </td>
                    <td>
                        <div>{{ $b['date'] }}</div>
                    </td>
                    <td>
                        <span class="badge bg-body-secondary text-body border">{{ $b['creator'] }}</span>
                    </td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-success" href="{{ route('backups.download', $b['name']) }}" title="داگرتن">
                            <i class="bi bi-download"></i> داگرتن
                        </a>

                        <!-- Trigger Two-Step Restore Modal -->
                        <button type="button" class="btn btn-sm btn-outline-warning text-dark" onclick="openRestoreModal('{{ $b['name'] }}')" title="گەڕاندنەوە">
                            <i class="bi bi-arrow-counterclockwise"></i> گەڕاندنەوە
                        </button>

                        <form class="d-inline" method="POST" action="{{ route('backups.destroy', $b['name']) }}" onsubmit="return confirm('ئایا دڵنیایت لە سڕینەوەی ئەم باکئەپە؟')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="سڕینەوە">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="bi bi-database-slash fs-1 opacity-25 d-block mb-2"></i>
                        هیچ باکئەپێک دروست نەکراوە. دوگمەی سەرەوە دابگرە بۆ دروستکردنی یەکەم باکئەپ.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Two-Step Restore Confirmation Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form method="POST" id="restoreForm">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger">⚠️ ئاگاداریی مەترسیدار: گەڕاندنەوەی باکئەپ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <p class="text-muted small mb-2">
                        گەڕاندنەوەی دەیتابەیس داتاکانی ئێستا بە تەواوی دەگۆڕێت بەو داتایانەی لەناو باکئەپەکەدان:
                    </p>
                    <div class="p-2 bg-body-tertiary rounded-3 font-monospace text-center mb-3 fw-bold text-primary" id="restoreFileName">
                        ...
                    </div>
                    <label class="form-label small fw-bold">بۆ پەسەندکردن تکایە وشەی <span class="text-danger">RESTORE</span> بنووسە:</label>
                    <input type="text" name="confirm_restore" class="form-control form-control-lg text-center font-monospace" placeholder="RESTORE" required autocomplete="off">
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">پاشگەزبوونەوە</button>
                    <button type="submit" class="btn btn-danger">بەڵێ، دەیتابەیس بگێڕەوە</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openRestoreModal(filename) {
        document.getElementById('restoreFileName').textContent = filename;
        document.getElementById('restoreForm').action = '/backups/' + encodeURIComponent(filename) + '/restore';
        new bootstrap.Modal(document.getElementById('restoreModal')).show();
    }
</script>
@endpush
@endsection
