<form method="POST" action="{{ $action }}">
    @csrf 
    @if($method !== 'POST') 
        @method($method) 
    @endif

    <div class="row g-3">
        <div class="col-12 col-md-6">
            <label class="form-label fw-semibold">ژمارەی تەبلە / ئۆتۆمبێل <span class="text-danger">*</span></label>
            <input class="form-control form-control-lg @error('number') is-invalid @enderror" name="number" required placeholder="وەک: SUL-1025" value="{{ old('number', $vehicle?->number) }}">
            @error('number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 col-md-6">
            <label class="form-label fw-semibold">جۆری ئۆتۆمبێل <span class="text-danger">*</span></label>
            <input class="form-control form-control-lg @error('type') is-invalid @enderror" name="type" required placeholder="تۆیۆتا، نیسان، هیۆندای..." value="{{ old('type', $vehicle?->type) }}">
            @error('type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 col-md-6">
            <label class="form-label fw-semibold">مۆدێل / ساڵ</label>
            <input class="form-control form-control-lg @error('model') is-invalid @enderror" name="model" placeholder="وەک: Corolla 2023" value="{{ old('model', $vehicle?->model) }}">
            @error('model')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 col-md-6">
            <label class="form-label fw-semibold">دۆخی کارپێکردن <span class="text-danger">*</span></label>
            <select class="form-select form-select-lg @error('status') is-invalid @enderror" name="status" required>
                <option value="available" @selected(old('status', $vehicle?->status ?? 'available') === 'available')>بەردەست بۆ دەرچوون (Available)</option>
                <option value="maintenance" @selected(old('status', $vehicle?->status) === 'maintenance')>لە چاککردنەوەدایە (Maintenance - ناتوانرێت دەربچێت)</option>
                <option value="inactive" @selected(old('status', $vehicle?->status) === 'inactive')>ناچالاک (Inactive)</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <label class="form-label fw-semibold">تێبینی یان وەسفی زیاتر</label>
            <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3" placeholder="تێبینی، کێشەی تەکنیکی، یان زانیاری زیاتر لەسەر ئۆتۆمبێلەکە...">{{ old('description', $vehicle?->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary btn-lg px-4">
            <i class="bi bi-check-circle me-1"></i> پاشەکەوتکردن
        </button>
        <a class="btn btn-light btn-lg px-4" href="{{ route('vehicles.index') }}">
            پاشگەزبوونەوە
        </a>
    </div>
</form>
