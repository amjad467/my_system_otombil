<!doctype html>
<html lang="ku" dir="rtl" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>چوونەژوورەوە | {{ \App\Models\Setting::get('org_name', 'سیستەمی بەڕێوەبردنی ئۆتۆمبێلەکان') }}</title>
    
    <!-- Google Font: Vazirmatn -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 RTL & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, Tahoma, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .login-header {
            background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
            color: #ffffff;
            padding: 36px 28px;
            text-align: center;
        }

        .logo-box {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin: 0 auto 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            font-family: inherit;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        .btn-submit {
            background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.35);
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="logo-box">🚗</div>
            <h3 class="fw-bold mb-1">{{ \App\Models\Setting::get('org_name', 'سیستەمی بەڕێوەبردنی ئۆتۆمبێلەکان') }}</h3>
            <p class="mb-0 text-white-50 small">بەخێربێن، تکایە بچۆ ژوورەوە بۆ هەژمارەکەت</p>
        </div>

        <div class="p-4 p-md-5">
            @if($errors->any())
                <div class="alert alert-danger rounded-3 py-2 small mb-4 d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" id="loginForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary">ئیمەیڵ</label>
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-envelope"></i></span>
                        <input class="form-control border-start-0" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary">وشەی نهێنی</label>
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-lock"></i></span>
                        <input class="form-control border-start-0" type="password" name="password" required placeholder="••••••••">
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label small text-secondary" for="remember">لەبیرم مەکە (بۆ ماوەیەکی درێژتر)</label>
                </div>

                <button type="submit" class="btn btn-submit w-100 mb-3" id="submitBtn">
                    <i class="bi bi-box-arrow-in-left me-1"></i> چوونەژوورەوە
                </button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> چاوەڕێبە...';
        });
    </script>
</body>
</html>
