<!doctype html>
<html lang="ku" dir="rtl" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="theme-color" content="#1e293b">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/svg+xml" href="/icons/icon.svg">
    <link rel="apple-touch-icon" href="/icons/icon.svg">
    
    <title>{{ $title ?? \App\Models\Setting::get('org_name', 'سیستەمی بەڕێوەبردنی ئۆتۆمبێل') }}</title>
    
    <!-- Google Font: Vazirmatn -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 RTL & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --app-font: 'Vazirmatn', -apple-system, BlinkMacSystemFont, Tahoma, sans-serif;
            --sidebar-width: 260px;
            --primary-accent: #2563eb;
            --primary-gradient: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
            --header-height: 68px;
            --card-radius: 16px;
            --btn-radius: 10px;
            --transition-smooth: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [data-bs-theme="light"] {
            --body-bg: #f8fafc;
            --sidebar-bg: #0f172a;
            --sidebar-text: #94a3b8;
            --sidebar-active-bg: rgba(37, 99, 235, 0.15);
            --sidebar-active-text: #60a5fa;
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            --card-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
            --text-heading: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --navbar-bg: #ffffff;
            --navbar-border: #e2e8f0;
            --table-header: #f1f5f9;
        }

        [data-bs-theme="dark"] {
            --body-bg: #090d16;
            --sidebar-bg: #0d1322;
            --sidebar-text: #94a3b8;
            --sidebar-active-bg: rgba(37, 99, 235, 0.25);
            --sidebar-active-text: #93c5fd;
            --card-bg: #131c2e;
            --card-border: #1e293b;
            --card-shadow: 0 4px 25px -4px rgba(0, 0, 0, 0.4);
            --text-heading: #f8fafc;
            --text-body: #cbd5e1;
            --text-muted: #94a3b8;
            --navbar-bg: #0d1322;
            --navbar-border: #1e293b;
            --table-header: #1e293b;
        }

        * { box-sizing: border-box; }

        body {
            font-family: var(--app-font);
            background-color: var(--body-bg);
            color: var(--text-body);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            letter-spacing: -0.01em;
            -webkit-tap-highlight-color: transparent;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .app-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        .app-sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: #ffffff;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            right: 0;
            z-index: 1040;
            transition: var(--transition-smooth);
            overflow-y: auto;
            border-left: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            text-decoration: none;
            color: #ffffff;
        }

        .brand-logo-badge {
            width: 40px;
            height: 40px;
            background: var(--primary-gradient);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }

        .sidebar-content {
            padding: 16px 12px;
            flex-grow: 1;
        }

        .sidebar-heading {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            padding: 12px 14px 6px;
        }

        .nav-item-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 16px;
            margin-bottom: 4px;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 12px;
            font-size: 14.5px;
            font-weight: 500;
            transition: var(--transition-smooth);
            position: relative;
        }

        .nav-item-link i {
            font-size: 18px;
            transition: transform 0.2s ease;
        }

        .nav-item-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.07);
        }

        .nav-item-link:hover i {
            transform: scale(1.1);
        }

        .nav-item-link.active {
            color: #ffffff;
            background: var(--primary-gradient);
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
        }

        .app-main {
            flex-grow: 1;
            margin-right: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-width: 0;
            transition: var(--transition-smooth);
        }

        .app-navbar {
            height: var(--header-height);
            background: var(--navbar-bg);
            border-bottom: 1px solid var(--navbar-border);
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1020;
            backdrop-filter: blur(12px);
            transition: var(--transition-smooth);
        }

        .search-trigger-btn {
            background: rgba(100, 116, 139, 0.08);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 8px 16px;
            font-size: 13.5px;
            color: var(--text-muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 240px;
            transition: var(--transition-smooth);
        }

        .search-trigger-btn:hover {
            background: rgba(100, 116, 139, 0.14);
            border-color: var(--primary-accent);
            color: var(--text-body);
        }

        .kbd-shortcut {
            background: rgba(0, 0, 0, 0.08);
            border-radius: 6px;
            padding: 2px 6px;
            font-size: 11px;
            font-weight: 600;
            margin-right: auto;
        }

        .nav-icon-btn {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(100, 116, 139, 0.08);
            color: var(--text-body);
            border: 1px solid var(--card-border);
            position: relative;
            text-decoration: none;
            transition: var(--transition-smooth);
        }

        .nav-icon-btn:hover {
            background: rgba(100, 116, 139, 0.16);
            color: var(--primary-accent);
            border-color: var(--primary-accent);
        }

        .notification-pulse {
            position: absolute;
            top: -3px;
            left: -3px;
            background: #ef4444;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 9999px;
            border: 2px solid var(--navbar-bg);
        }

        .notification-dropdown {
            width: 360px;
            max-width: calc(100vw - 32px);
            max-height: 480px;
            border-radius: 16px;
            border: 1px solid var(--card-border);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            background: var(--card-bg);
            padding: 0;
            overflow: hidden;
        }

        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid var(--card-border);
            display: flex;
            gap: 12px;
            align-items: flex-start;
            text-decoration: none;
            color: var(--text-body);
            transition: background 0.15s ease;
        }

        .notification-item:hover {
            background: rgba(37, 99, 235, 0.05);
        }

        .notification-item.unread {
            background: rgba(37, 99, 235, 0.08);
        }

        .notif-icon-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--card-radius);
            box-shadow: var(--card-shadow);
            transition: var(--transition-smooth);
        }

        .btn {
            border-radius: var(--btn-radius);
            font-family: var(--app-font);
            font-weight: 600;
            padding: 8px 16px;
            transition: var(--transition-smooth);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
        }

        .form-control, .form-select {
            border-radius: 10px;
            border-color: var(--card-border);
            background-color: var(--card-bg);
            color: var(--text-body);
            padding: 10px 14px;
            font-family: var(--app-font);
        }

        @media (max-width: 991.98px) {
            .app-sidebar {
                transform: translateX(100%);
            }
            .app-main {
                margin-right: 0;
            }
            .search-trigger-btn {
                min-width: auto;
                padding: 8px 12px;
            }
            .search-trigger-btn span {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="app-sidebar no-print" id="appSidebar">
            <a href="{{ Route::has('dashboard') ? route('dashboard') : '#' }}" class="sidebar-brand">
                <div class="brand-logo-badge">🚗</div>
                <div>
                    <div class="fw-bold fs-6 lh-sm">{{ \App\Models\Setting::get('org_name', 'سیستەمی ئۆتۆمبێلەکان') }}</div>
                    <small class="text-secondary" style="font-size: 11px;">بەڕێوەبردنی دەرچوون و گەڕانەوە</small>
                </div>
            </a>

            <div class="sidebar-content">
                <div class="sidebar-heading">سەرەکی</div>
                @if(Route::has('dashboard'))
                <a href="{{ route('dashboard') }}" class="nav-item-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>داشبۆرد</span>
                </a>
                @endif

                @auth
                    @if(auth()->user()->isAdmin())
                        <div class="sidebar-heading mt-3">بەڕێوەبردن</div>
                        @if(Route::has('movements.index'))
                        <a href="{{ route('movements.index') }}" class="nav-item-link {{ request()->routeIs('movements.*') ? 'active' : '' }}">
                            <i class="bi bi-arrow-left-right"></i>
                            <span>جوڵەی گەشتەکان</span>
                        </a>
                        @endif
                        @if(Route::has('vehicles.index'))
                        <a href="{{ route('vehicles.index') }}" class="nav-item-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
                            <i class="bi bi-car-front-fill"></i>
                            <span>ئۆتۆمبێلەکان</span>
                        </a>
                        @endif
                        @if(Route::has('drivers.index'))
                        <a href="{{ route('drivers.index') }}" class="nav-item-link {{ request()->routeIs('drivers.*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill"></i>
                            <span>بەکارهێنەر و شۆفێر</span>
                        </a>
                        @endif
                        @if(Route::has('reports.index'))
                        <a href="{{ route('reports.index') }}" class="nav-item-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-bar-graph-fill"></i>
                            <span>ڕاپۆرتەکان</span>
                        </a>
                        @endif

                        <div class="sidebar-heading mt-3">سیستەم و ئاسایش</div>
                        @if(Route::has('audit.index'))
                        <a href="{{ route('audit.index') }}" class="nav-item-link {{ request()->routeIs('audit.*') ? 'active' : '' }}">
                            <i class="bi bi-shield-check"></i>
                            <span>تۆماری چاودێری (Audit)</span>
                        </a>
                        @endif
                        @if(Route::has('backups.index'))
                        <a href="{{ route('backups.index') }}" class="nav-item-link {{ request()->routeIs('backups.*') ? 'active' : '' }}">
                            <i class="bi bi-database-fill-check"></i>
                            <span>باکئەپ و پاراستن</span>
                        </a>
                        @endif
                        @if(Route::has('settings.index'))
                        <a href="{{ route('settings.index') }}" class="nav-item-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <i class="bi bi-gear-fill"></i>
                            <span>ڕێکخستنەکان</span>
                        </a>
                        @endif
                    @endif

                    <div class="sidebar-heading mt-3">گەشتەکان</div>
                    @if(Route::has('driver.departure'))
                    <a href="{{ route('driver.departure') }}" class="nav-item-link {{ request()->routeIs('driver.departure') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle-fill"></i>
                        <span>تۆمارکردنی دەرچوون</span>
                    </a>
                    @endif
                    @if(Route::has('driver.active'))
                    <a href="{{ route('driver.active') }}" class="nav-item-link {{ request()->routeIs('driver.active') ? 'active' : '' }}">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>گەشتی ئێستام</span>
                    </a>
                    @endif
                    @if(Route::has('driver.history'))
                    <a href="{{ route('driver.history') }}" class="nav-item-link {{ request()->routeIs('driver.history') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i>
                        <span>مێژووی گەشتەکانم</span>
                    </a>
                    @endif
                    @if(Route::has('notifications.index'))
                    <a href="{{ route('notifications.index') }}" class="nav-item-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                        <i class="bi bi-bell-fill"></i>
                        <span>ئاگادارکردنەوەکان</span>
                    </a>
                    @endif

                    <!-- بەشی ژیریی دەستکرد / AI Assistant -->
                    <div class="sidebar-heading mt-3">یارمەتیدەر</div>
                    <a href="javascript:void(0)" class="nav-item-link">
                        <i class="bi bi-robot text-info"></i>
                        <span>یارمەتیدەری ژیر (AI)</span>
                    </a>
                @endauth
            </div>

            @auth
            <div class="p-3 border-top border-secondary border-opacity-25 mt-auto">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                            {{ mb_substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <div class="text-white small fw-bold text-truncate" style="max-width: 130px;">{{ auth()->user()->name ?? '' }}</div>
                            <span class="badge bg-secondary" style="font-size: 10px;">{{ auth()->user()->isAdmin() ? 'بەڕێوەبەر' : 'شۆفێر' }}</span>
                        </div>
                    </div>
                    @if(Route::has('logout'))
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger p-1 px-2" title="دەرچوون">
                            <i class="bi bi-box-arrow-left"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endauth
        </aside>

        <!-- Main Content Layout -->
        <div class="app-main">
            <!-- Navbar Header -->
            <header class="app-navbar no-print">
                <div class="d-flex align-items-center gap-2">
                    <div class="position-relative">
                        <div class="search-trigger-btn">
                            <i class="bi bi-search"></i>
                            <span>گەڕانی خێرا (شۆفێر، ئۆتۆمبێل، گەشت)...</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <!-- AI Assistant Button -->
                    <a href="javascript:void(0)" class="nav-icon-btn text-info" title="یارمەتیدەری ژیر (AI)">
                        <i class="bi bi-robot fs-5"></i>
                    </a>

                    @auth
                    @php
                        $unread = auth()->user()->unreadNotifications ?? collect();
                    @endphp
                    <div class="dropdown">
                        <button class="nav-icon-btn" data-bs-toggle="dropdown" aria-expanded="false" title="ئاگادارکردنەوەکان">
                            <i class="bi bi-bell-fill fs-5"></i>
                            @if($unread->count() > 0)
                                <span class="notification-pulse">{{ $unread->count() > 99 ? '99+' : $unread->count() }}</span>
                            @endif
                        </button>
                        <div class="dropdown-menu dropdown-menu-start notification-dropdown shadow-lg">
                            <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-body-tertiary">
                                <div class="fw-bold fs-6">
                                    ئاگادارکردنەوەکان
                                    @if($unread->count())
                                        <span class="badge bg-danger rounded-pill ms-1">{{ $unread->count() }}</span>
                                    @endif
                                </div>
                                @if($unread->count() && Route::has('notifications.readAll'))
                                    <form method="POST" action="{{ route('notifications.readAll') }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-link text-primary text-decoration-none p-0" style="font-size: 12px;">
                                            خوێندنەوەی هەمووی
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <div style="max-height: 330px; overflow-y: auto;">
                                @forelse($unread->take(7) as $n)
                                    @if(Route::has('notifications.read'))
                                    <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                                        @csrf
                                        <button type="submit" class="notification-item unread w-100 text-start border-0 bg-transparent">
                                            <div class="notif-icon-circle bg-primary-subtle text-primary">
                                                <i class="bi bi-info-circle-fill"></i>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <div class="fw-bold small text-truncate">{{ $n->data['title'] ?? 'ئاگادارکردنەوە' }}</div>
                                                <div class="text-muted small text-truncate" style="font-size: 11.5px;">{{ $n->data['body'] ?? '' }}</div>
                                                <div class="text-secondary mt-1" style="font-size: 10px;">{{ $n->created_at->diffForHumans() }}</div>
                                            </div>
                                        </button>
                                    </form>
                                    @endif
                                @empty
                                    <div class="p-4 text-center text-muted">
                                        <i class="bi bi-bell-slash fs-2 d-block mb-1 text-secondary opacity-50"></i>
                                        <div class="small">هیچ ئاگادارکردنەوەیەکی نوێ نییە</div>
                                    </div>
                                @endforelse
                            </div>

                            @if(Route::has('notifications.index'))
                            <div class="p-2 border-top text-center bg-body-tertiary">
                                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-light w-100 text-primary fw-bold" style="font-size: 12.5px;">
                                    بینینی هەموو ئاگادارکردنەوەکان <i class="bi bi-arrow-left ms-1"></i>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="dropdown">
                        <button class="d-flex align-items-center gap-2 border-0 bg-transparent p-1 dropdown-toggle text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 38px; height: 38px;">
                                {{ mb_substr(auth()->user()->name ?? 'U', 0, 1) }}
                            </div>
                            <span class="d-none d-md-inline fw-semibold small text-body">{{ auth()->user()->name ?? '' }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-start shadow-lg border-0 rounded-4 p-2" style="width: 220px;">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold text-truncate">{{ auth()->user()->name ?? '' }}</div>
                                <div class="text-muted small text-truncate">{{ auth()->user()->email ?? '' }}</div>
                            </li>
                            @if(Route::has('driver.departure'))
                            <li><a class="dropdown-item rounded-2 py-2 mt-1" href="{{ route('driver.departure') }}"><i class="bi bi-plus-circle me-2 text-primary"></i>تۆمارکردنی دەرچوون</a></li>
                            @endif
                            @if(Route::has('driver.history'))
                            <li><a class="dropdown-item rounded-2 py-2" href="{{ route('driver.history') }}"><i class="bi bi-clock-history me-2 text-info"></i>گەشتەکانم</a></li>
                            @endif
                            @if(auth()->user()->isAdmin() && Route::has('settings.index'))
                            <li><a class="dropdown-item rounded-2 py-2" href="{{ route('settings.index') }}"><i class="bi bi-gear me-2 text-secondary"></i>ڕێکخستنەکان</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            @if(Route::has('logout'))
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item rounded-2 py-2 text-danger">
                                        <i class="bi bi-box-arrow-left me-2"></i>دەرچوون لە هەژمار
                                    </button>
                                </form>
                            </li>
                            @endif
                        </ul>
                    </div>
                    @endauth
                </div>
            </header>

            <!-- Page Content Placeholder -->
            <main class="p-3 p-md-4">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>