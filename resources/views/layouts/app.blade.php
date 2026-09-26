<!doctype html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'سیستەمی بەڕێوەبردنی ئۆتۆمبێل' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body { font-family: Tahoma, Arial, sans-serif; background: #f5f7fb; }
        .navbar { background: #123b5d; }
        .card { border: 0; box-shadow: 0 4px 18px #123b5d12; }
        .stat { font-size: 30px; font-weight: 700; }
        .table td, .table th { vertical-align: middle; }
        .mobile-action { min-height: 54px; font-size: 18px; }
        .sidebar-link { display: block; padding: 10px 14px; border-radius: 8px; text-decoration: none; color: #334155; }
        .sidebar-link:hover { background: #eef4f8; }
        .badge-out { background: #fff0d5; color: #8a5a00; }
        .badge-returned { background: #e5f7ed; color: #19734a; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .card { box-shadow: none; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4 no-print">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">🚗 سیستەمی ئۆتۆمبێلەکان</a>
            
            @auth
            <!-- دوگمەی منیو بۆ مۆبایل -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="پێشاندانی منیو">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse mt-2 mt-lg-0" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1">
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('drivers.index') }}">بەکارهێنەران</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('vehicles.index') }}">ئۆتۆمبێلەکان</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('reports.index') }}">راپۆرت</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('backups.index') }}">باکئەپ</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('driver.home') }}">گەشتەکانم</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('driver.departure') }}">دەرچوونی خۆم</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('driver.home') }}">گەشتەکانم</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('driver.departure') }}">دەرچوون</a></li>
                    @endif
                </ul>

                <div class="d-flex align-items-lg-center align-items-start flex-column flex-lg-row gap-3 text-white border-top border-lg-0 pt-3 pt-lg-0 mt-2 mt-lg-0">
                    <!-- بەشی ئاگادارکردنەوەکان -->
                    @php($unread = auth()->user()->unreadNotifications)
                    <div class="dropdown">
                        <a class="text-white text-decoration-none position-relative fs-5" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            🔔
                            @if($unread->count())
                                <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" style="font-size:10px">{{ $unread->count() }}</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2 shadow-lg" style="max-width: 90vw; width: 320px; max-height: 400px; overflow: auto;">
                            <li class="d-flex justify-content-between align-items-center px-2 pb-2 border-bottom">
                                <strong>ئاگادارکردنەوەکان</strong>
                                @if($unread->count())
                                    <form method="POST" action="{{ route('notifications.readAll') }}">
                                        @csrf
                                        <button class="btn btn-link btn-sm p-0 text-decoration-none">هەمووی وەک خوێندراوە</button>
                                    </form>
                                @endif
                            </li>
                            @forelse($unread->take(10) as $n)
                                <li>
                                    <form method="POST" action="{{ route('notifications.read',$n->id) }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item small text-wrap border-bottom py-2">
                                            <div class="fw-bold">{{ $n->data['title'] ?? '' }}</div>
                                            <div class="text-muted">{{ $n->data['body'] ?? '' }}</div>
                                        </button>
                                    </form>
                                </li>
                            @empty
                                <li><span class="dropdown-item-text text-muted small">هیچ ئاگادارکردنەوەیەکی نوێ نییە</span></li>
                            @endforelse
                            <li class="pt-2 px-2"><a class="small text-decoration-none" href="{{ route('movements.index') }}#not-returned">بینینی لیستی نەگەڕاوەکان</a></li>
                        </ul>
                    </div>

                    <!-- زانیاری بەکارهێنەر و چوونەدەرەوە -->
                    <span class="fw-bold fs-6">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button class="btn btn-sm btn-outline-light w-100">دەرچوون</button>
                    </form>
                </div>
            </div>
            @endauth
        </div>
    </nav>

    <main class="container pb-5">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif 
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif 
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif 
        
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>