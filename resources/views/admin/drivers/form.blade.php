<form method="POST" action="{{ $action }}">
    @csrf 
    @if($method !== 'POST') 
        @method($method) 
    @endif

    <div class="row g-3">
        <div class="col-12 col-md-6">
            <label class="form-label fw-semibold">ناوی تەواو <span class="text-danger">*</span></label>
            <input class="form-control form-control-lg @error('name') is-invalid @enderror" name="name" required placeholder="وەک: ئارام ئەحمەد" value="{{ old('name', $driver?->name) }}">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 col-md-6">
            <label class="form-label fw-semibold">ناونیشانی ئیمەیڵ <span class="text-danger">*</span></label>
            <input class="form-control form-control-lg @error('email') is-invalid @enderror" type="email" name="email" required placeholder="aram@example.com" value="{{ old('email', $driver?->email) }}">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 col-md-6">
            <label class="form-label fw-semibold">ژمارەی مۆبایل</label>
            <input class="form-control form-control-lg @error('phone') is-invalid @enderror" name="phone" placeholder="0750 000 0000" value="{{ old('phone', $driver?->phone) }}">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 col-md-6">
            <label class="form-label fw-semibold">سەلاحیەت / ڕۆڵ <span class="text-danger">*</span></label>
            <select class="form-select form-select-lg @error('role') is-invalid @enderror" name="role" required>
                <option value="driver" @selected(old('role', $driver?->role ?? 'driver') === 'driver')>شۆفێری ئاسایی (دەرچوون، گەڕانەوە، گەشتەکانی خۆی)</option>
                <option value="admin" @selected(old('role', $driver?->role) === 'admin')>بەڕێوەبەری گشتی (Admin - دەسەڵاتی تەواو)</option>
            </select>
            @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        @if($driver)
        <div class="col-12 col-md-6">
            <label class="form-label fw-semibold">دۆخی هەژمار</label>
            <select class="form-select form-select-lg" name="active">
                <option value="1" @selected(old('active', $driver->active ? '1' : '0') === '1')>چالاک (دەتوانێت بچێتە ژوورەوە)</option>
                <option value="0" @selected(old('active', $driver->active ? '1' : '0') === '0')>ناچالاک (ناتوانێت بچێتە ژوورەوە)</option>
            </select>
        </div>
        @endif

        <div class="col-12"><hr class="my-3"></div>

        <div class="col-12 col-md-6">
            <label class="form-label fw-semibold">
                {{ $driver ? 'وشەی نهێنی نوێ (ئەگەر دەتەوێت بگۆڕدرێت)' : 'وشەی نهێنی' }}
                @if(!$driver) <span class="text-danger">*</span> @endif
            </label>
            <input class="form-control form-control-lg @error('password') is-invalid @enderror" type="password" name="password" {{ $driver ? '' : 'required' }} placeholder="بە لایەنی کەم ٦ پیت یان ژمارە">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 col-md-6">
            <label class="form-label fw-semibold">
                دووبارەکردنەوەی وشەی نهێنی
                @if(!$driver) <span class="text-danger">*</span> @endif
            </label>
            <input class="form-control form-control-lg" type="password" name="password_confirmation" {{ $driver ? '' : 'required' }} placeholder="دووبارە بنووسەوە">
        </div>
    </div>

    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary btn-lg px-4">
            <i class="bi bi-check-circle me-1"></i> پاشەکەوتکردن
        </button>
        <a class="btn btn-light btn-lg px-4" href="{{ route('drivers.index') }}">
            پاشگەزبوونەوە
        </a>
    </div>
</form>
