<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#151828">
    <title>@yield('title', 'Admin') — Abhiram Chandramohan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body.admin-body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; background: #f5f3ef; color: #0f172a; margin: 0; -webkit-font-smoothing: antialiased; -webkit-tap-highlight-color: transparent; }
        .admin-sidebar { background: #151828; }
        .admin-topbar { background: #ffffff; border-bottom: 1px solid #e5e7eb; }
        .admin-card { background: #ffffff; border: 1px solid #e2e0db; border-radius: 12px; }
        .admin-nav-active { background: rgba(208, 173, 93, 0.15); color: #d0ad5d; }
        .admin-nav-item { color: rgba(248, 246, 241, 0.5); }
        .admin-nav-item:hover { color: #F8F6F1; background: rgba(248, 246, 241, 0.06); }
        .admin-input { background: #faf9f7; border: 1px solid #e2e0db; color: #0f172a; border-radius: 10px; padding: 10px 14px; font-size: 16px; width: 100%; outline: none; transition: border-color 0.2s, box-shadow 0.2s; -webkit-appearance: none; }
        .admin-input::placeholder { color: #94a3b8; }
        .admin-input:focus { border-color: #d0ad5d; box-shadow: 0 0 0 3px rgba(208, 173, 93, 0.12); }
        .admin-btn-gold { background: linear-gradient(135deg, #d0ad5d, #b8952e); color: #fff; font-weight: 600; font-size: 14px; padding: 12px 20px; border-radius: 10px; border: none; cursor: pointer; transition: all 0.2s; -webkit-appearance: none; }
        .admin-btn-gold:hover { background: linear-gradient(135deg, #b8952e, #a07e1e); box-shadow: 0 4px 20px rgba(208, 173, 93, 0.3); }
        .admin-btn-gold:active { transform: scale(0.97); }
        .admin-btn-outline { background: transparent; border: 1px solid #e2e0db; color: #475569; font-size: 13px; font-weight: 500; padding: 10px 16px; border-radius: 8px; cursor: pointer; transition: all 0.2s; -webkit-appearance: none; }
        .admin-btn-outline:hover { border-color: #d0ad5d; color: #92700c; background: #fef8ec; }
        .admin-btn-outline:active { transform: scale(0.97); }
        .admin-badge { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; background: #f1f5f9; color: #475569; border: 1px solid #e2e0db; }

        /* Sidebar section labels */
        .sidebar-section { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.2em; color: rgba(208, 173, 93, 0.5); }

        /* Mobile overlay */
        .mobile-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 40; backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); }
        .mobile-overlay.active { display: block; }

        /* Mobile drawer */
        .mobile-drawer { position: fixed; top: 0; left: 0; bottom: 0; width: 280px; background: #151828; z-index: 50; transform: translateX(-100%); transition: transform 0.25s ease; overflow-y: auto; -webkit-overflow-scrolling: touch; }
        .mobile-drawer.active { transform: translateX(0); }

        /* Bottom nav for mobile */
        .mobile-bottom-nav { display: none; position: fixed; bottom: 0; left: 0; right: 0; background: #151828; border-top: 1px solid rgba(248, 246, 241, 0.08); z-index: 30; padding-bottom: env(safe-area-inset-bottom, 0px); }

        /* Main content needs bottom padding on mobile for bottom nav */
        @media (max-width: 1023px) {
            .mobile-bottom-nav { display: flex; }
            .admin-main-content { padding-bottom: 80px !important; }
            .admin-card { border-radius: 10px; }
        }
        @media (min-width: 1024px) {
            .admin-input { font-size: 14px; }
        }
    </style>
    @stack('head')
</head>
<body class="admin-body antialiased min-h-screen">

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay" onclick="closeMobileDrawer()"></div>

    <!-- Mobile Drawer -->
    <div class="mobile-drawer" id="mobileDrawer">
        <div class="flex items-center justify-between px-4 py-5" style="border-bottom: 1px solid rgba(248,246,241,0.08);">
            <a href="{{ route('admin.dashboard.today') }}" class="flex items-center gap-1 no-underline">
                <span class="font-heading text-lg font-bold text-ivory">Abhiram</span>
                <span class="font-heading text-lg font-bold text-gold">.</span>
            </a>
            <button onclick="closeMobileDrawer()" class="w-9 h-9 rounded-lg border-0 cursor-pointer flex items-center justify-center" style="background: rgba(248,246,241,0.06);">
                <svg class="w-[18px] h-[18px] text-ivory/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <nav class="p-4">
            <a href="{{ route('admin.dashboard.today') }}" class="{{ request()->routeIs('admin.dashboard.today') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-3 px-3 rounded-lg text-[15px] font-medium no-underline mb-1">
                <span class="relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full" style="background: {{ $todayHexColor }};"></span>
                </span>
                <span>Today</span>
            </a>
            <div class="mt-4 mb-2 px-3">
                <span class="sidebar-section">Planning</span>
            </div>
            <a href="{{ route('admin.tasks.index') }}" class="{{ request()->routeIs('admin.tasks.index') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-3 px-3 rounded-lg text-[15px] font-medium no-underline mb-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span>Tasks</span>
            </a>
            <a href="{{ route('admin.tasks.templates') }}" class="{{ request()->routeIs('admin.tasks.templates*') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-3 px-3 rounded-lg text-[15px] font-medium no-underline mb-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                <span>Templates</span>
            </a>
            <a href="{{ route('admin.practices.index') }}" class="{{ request()->routeIs('admin.practices.*') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-3 px-3 rounded-lg text-[15px] font-medium no-underline mb-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                <span>Practices</span>
            </a>
            <a href="{{ route('admin.upskilling.index') }}" class="{{ request()->routeIs('admin.upskilling.*') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-3 px-3 rounded-lg text-[15px] font-medium no-underline mb-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Upskilling</span>
            </a>
            <div class="mt-4 mb-2 px-3">
                <span class="sidebar-section">Reflect</span>
            </div>
            <a href="{{ route('admin.weekly-review.index') }}" class="{{ request()->routeIs('admin.weekly-review.index') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-3 px-3 rounded-lg text-[15px] font-medium no-underline mb-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Weekly Review</span>
            </a>
            <a href="{{ route('admin.weekly-review.history') }}" class="{{ request()->routeIs('admin.weekly-review.history') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-3 pl-11 pr-3 rounded-lg text-sm font-medium no-underline mb-1">
                <span>History</span>
            </a>
            <div class="mt-4 mb-2 px-3">
                <span class="sidebar-section">Insights</span>
            </div>
            <a href="{{ route('admin.analytics.index') }}" class="{{ request()->routeIs('admin.analytics.index') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-3 px-3 rounded-lg text-[15px] font-medium no-underline mb-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Analytics</span>
            </a>
            <a href="{{ route('admin.analytics.monthly', ['month' => now()->format('Y-m')]) }}" class="{{ request()->routeIs('admin.analytics.monthly') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-3 pl-11 pr-3 rounded-lg text-sm font-medium no-underline mb-1">
                <span>Monthly</span>
            </a>
            <div class="mt-4 mb-2 px-3">
                <span class="sidebar-section">Settings</span>
            </div>
            <a href="{{ route('admin.settings.working-days.index') }}" class="{{ request()->routeIs('admin.settings.working-days*') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-3 px-3 rounded-lg text-[15px] font-medium no-underline mb-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Working Days</span>
            </a>
            <a href="{{ route('admin.settings.working-hours.index') }}" class="{{ request()->routeIs('admin.settings.working-hours*') || request()->routeIs('admin.settings.time-blocks*') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-3 px-3 rounded-lg text-[15px] font-medium no-underline mb-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Working Hours</span>
            </a>
        </nav>
        <div class="p-4 mt-auto" style="border-top: 1px solid rgba(248,246,241,0.08);">
            <div class="flex items-center gap-3 px-3 py-2 mb-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background: rgba(208,173,93,0.15);">
                    <span class="text-xs font-bold text-gold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-ivory m-0 truncate">{{ auth()->user()->name }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="admin-nav-item w-full flex items-center gap-3 py-3 px-3 rounded-lg text-[15px] border-0 bg-transparent cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </div>

    <div class="min-h-screen flex">

        <!-- ─── Desktop Sidebar ─── -->
        <aside class="hidden lg:flex flex-col w-64 admin-sidebar sticky top-0 h-screen">
            <div class="h-16 flex items-center px-6" style="border-bottom: 1px solid rgba(248,246,241,0.08);">
                <a href="{{ route('admin.dashboard.today') }}" class="flex items-center gap-1.5 no-underline">
                    <span class="font-heading text-lg font-bold text-ivory">Abhiram</span>
                    <span class="font-heading text-lg font-bold text-gold">.</span>
                    <span class="text-[10px] uppercase tracking-[0.15em] ml-1 mt-0.5" style="color: rgba(248,246,241,0.3);">Admin</span>
                </a>
            </div>
            <nav class="flex-1 px-4 py-6 overflow-y-auto">
                <a href="{{ route('admin.dashboard.today') }}" class="{{ request()->routeIs('admin.dashboard.today') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm font-medium no-underline mb-1 transition-all duration-150">
                    <span class="relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full" style="background: {{ $todayHexColor }};"></span>
                    </span>
                    <span>Today</span>
                </a>
                <div class="mt-5 mb-2 px-3">
                    <span class="sidebar-section">Planning</span>
                </div>
                <a href="{{ route('admin.tasks.index') }}" class="{{ request()->routeIs('admin.tasks.index') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm font-medium no-underline mb-1 transition-all duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>Tasks</span>
                </a>
                <a href="{{ route('admin.tasks.templates') }}" class="{{ request()->routeIs('admin.tasks.templates*') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm font-medium no-underline mb-1 transition-all duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                    <span>Templates</span>
                </a>
                <a href="{{ route('admin.practices.index') }}" class="{{ request()->routeIs('admin.practices.*') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm font-medium no-underline mb-1 transition-all duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    <span>Practices</span>
                </a>
                <a href="{{ route('admin.upskilling.index') }}" class="{{ request()->routeIs('admin.upskilling.*') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm font-medium no-underline mb-1 transition-all duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Upskilling</span>
                </a>
                <div class="mt-5 mb-2 px-3">
                    <span class="sidebar-section">Reflect</span>
                </div>
                <a href="{{ route('admin.weekly-review.index') }}" class="{{ request()->routeIs('admin.weekly-review.index') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm font-medium no-underline mb-1 transition-all duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Weekly Review</span>
                </a>
                <a href="{{ route('admin.weekly-review.history') }}" class="{{ request()->routeIs('admin.weekly-review.history') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-2.5 pl-11 pr-3 rounded-lg text-[13px] font-medium no-underline mb-1 transition-all duration-150">
                    <span>History</span>
                </a>
                <div class="mt-5 mb-2 px-3">
                    <span class="sidebar-section">Insights</span>
                </div>
                <a href="{{ route('admin.analytics.index') }}" class="{{ request()->routeIs('admin.analytics.index') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm font-medium no-underline mb-1 transition-all duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Analytics</span>
                </a>
                <a href="{{ route('admin.analytics.monthly', ['month' => now()->format('Y-m')]) }}" class="{{ request()->routeIs('admin.analytics.monthly') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-2.5 pl-11 pr-3 rounded-lg text-[13px] font-medium no-underline mb-1 transition-all duration-150">
                    <span>Monthly</span>
                </a>
                <div class="mt-5 mb-2 px-3">
                    <span class="sidebar-section">Settings</span>
                </div>
                <a href="{{ route('admin.settings.working-days.index') }}" class="{{ request()->routeIs('admin.settings.working-days*') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm font-medium no-underline mb-1 transition-all duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Working Days</span>
                </a>
                <a href="{{ route('admin.settings.working-hours.index') }}" class="{{ request()->routeIs('admin.settings.working-hours*') || request()->routeIs('admin.settings.time-blocks*') ? 'admin-nav-active' : 'admin-nav-item' }} flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm font-medium no-underline mb-1 transition-all duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Working Hours</span>
                </a>
            </nav>
            <div class="p-4" style="border-top: 1px solid rgba(248,246,241,0.08);">
                <div class="flex items-center gap-3 px-3 py-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background: rgba(208,173,93,0.15);">
                        <span class="text-xs font-bold text-gold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-ivory m-0 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs m-0 truncate" style="color: rgba(248,246,241,0.35);">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="admin-nav-item w-full flex items-center gap-3 py-2 px-3 rounded-lg text-sm border-0 bg-transparent cursor-pointer transition-all duration-150">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ─── Main Content ─── -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Bar -->
            <header class="admin-topbar h-14 flex items-center justify-between px-4 sticky top-0 z-20">
                <button onclick="openMobileDrawer()" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-lg border-0 bg-transparent cursor-pointer">
                    <svg class="w-[22px] h-[22px] text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <h1 class="font-heading text-[15px] font-semibold text-slate-900 m-0 flex-1 text-center">@yield('title', 'Dashboard')</h1>

                <a href="/" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-lg no-underline text-slate-400 hover:text-gold transition-colors">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </header>

            <!-- Content -->
            <main class="admin-main-content flex-1 p-4 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- ─── Mobile Bottom Navigation ─── -->
    <nav class="mobile-bottom-nav">
        @php
            $bottomNavItems = [
                ['route' => 'admin.dashboard.today', 'label' => 'Today', 'icon' => 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z'],
                ['route' => 'admin.tasks.index', 'label' => 'Tasks', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                ['route' => 'admin.weekly-review.index', 'label' => 'Review', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                ['route' => 'admin.analytics.index', 'label' => 'Insights', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ];
        @endphp
        <div class="flex w-full justify-around py-2 pb-1">
            @foreach($bottomNavItems as $item)
                @php $isActive = request()->routeIs($item['route'] . '*'); @endphp
                <a href="{{ route($item['route']) }}" class="flex flex-col items-center gap-0.5 py-1.5 px-3 no-underline min-w-[64px]">
                    <svg class="w-6 h-6" style="color: {{ $isActive ? '#d0ad5d' : 'rgba(248,246,241,0.4)' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isActive ? '2' : '1.5' }}" d="{{ $item['icon'] }}"/>
                    </svg>
                    <span class="text-[10px]" style="font-weight: {{ $isActive ? '600' : '500' }}; color: {{ $isActive ? '#d0ad5d' : 'rgba(248,246,241,0.4)' }};">{{ $item['label'] }}</span>
                </a>
            @endforeach
            <button onclick="openMobileDrawer()" class="flex flex-col items-center gap-0.5 py-1.5 px-3 bg-transparent border-0 cursor-pointer min-w-[64px]">
                <svg class="w-6 h-6" style="color: rgba(248,246,241,0.4);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <span class="text-[10px] font-medium" style="color: rgba(248,246,241,0.4);">More</span>
            </button>
        </div>
    </nav>

    <script>
        function openMobileDrawer() {
            document.getElementById('mobileDrawer').classList.add('active');
            document.getElementById('mobileOverlay').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeMobileDrawer() {
            document.getElementById('mobileDrawer').classList.remove('active');
            document.getElementById('mobileOverlay').classList.remove('active');
            document.body.style.overflow = '';
        }
    </script>

    @stack('scripts')
</body>
</html>
