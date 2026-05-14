<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#ffffff">
    <title>@yield('title', 'Admin') — Abhiram Chandramohan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body.admin-light { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; background: #f8f9fb; color: #1e293b; margin: 0; -webkit-font-smoothing: antialiased; -webkit-tap-highlight-color: transparent; }
        .admin-sidebar { background: #ffffff; border-right: 1px solid #e5e7eb; }
        .admin-topbar { background: #ffffff; border-bottom: 1px solid #e5e7eb; }
        .admin-card { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; }
        .admin-nav-active { background: #fef8ec; color: #92700c; }
        .admin-nav-item { color: #64748b; }
        .admin-nav-item:hover { color: #1e293b; background: #f1f5f9; }
        .admin-input { background: #f8f9fb; border: 1px solid #e5e7eb; color: #1e293b; border-radius: 10px; padding: 10px 14px; font-size: 16px; width: 100%; outline: none; transition: border-color 0.2s, box-shadow 0.2s; -webkit-appearance: none; }
        .admin-input::placeholder { color: #94a3b8; }
        .admin-input:focus { border-color: #d0ad5d; box-shadow: 0 0 0 3px rgba(208, 173, 93, 0.12); }
        .admin-btn-gold { background: #d0ad5d; color: #fff; font-weight: 600; font-size: 14px; padding: 12px 20px; border-radius: 10px; border: none; cursor: pointer; transition: all 0.2s; -webkit-appearance: none; }
        .admin-btn-gold:hover { background: #b8952e; box-shadow: 0 4px 16px rgba(208, 173, 93, 0.25); }
        .admin-btn-gold:active { transform: scale(0.97); }
        .admin-btn-outline { background: transparent; border: 1px solid #e5e7eb; color: #64748b; font-size: 13px; font-weight: 500; padding: 10px 16px; border-radius: 8px; cursor: pointer; transition: all 0.2s; -webkit-appearance: none; }
        .admin-btn-outline:hover { border-color: #d0ad5d; color: #92700c; background: #fef8ec; }
        .admin-btn-outline:active { transform: scale(0.97); }
        .admin-badge { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; background: #f1f5f9; color: #64748b; border: 1px solid #e5e7eb; }

        /* Mobile overlay */
        .mobile-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.3); z-index: 40; backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px); }
        .mobile-overlay.active { display: block; }

        /* Mobile drawer */
        .mobile-drawer { position: fixed; top: 0; left: 0; bottom: 0; width: 280px; background: #fff; z-index: 50; transform: translateX(-100%); transition: transform 0.25s ease; overflow-y: auto; -webkit-overflow-scrolling: touch; }
        .mobile-drawer.active { transform: translateX(0); }

        /* Bottom nav for mobile */
        .mobile-bottom-nav { display: none; position: fixed; bottom: 0; left: 0; right: 0; background: #ffffff; border-top: 1px solid #e5e7eb; z-index: 30; padding-bottom: env(safe-area-inset-bottom, 0px); }

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
<body class="admin-light antialiased min-h-screen">

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay" onclick="closeMobileDrawer()"></div>

    <!-- Mobile Drawer -->
    <div class="mobile-drawer" id="mobileDrawer">
        <div style="padding: 20px 16px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between;">
            <a href="{{ route('admin.dashboard') }}" style="text-decoration: none; display: flex; align-items: center; gap: 4px;">
                <span style="font-family: 'Space Grotesk', sans-serif; font-size: 18px; font-weight: 700; color: #1e293b;">Abhiram</span>
                <span style="font-family: 'Space Grotesk', sans-serif; font-size: 18px; font-weight: 700; color: #d0ad5d;">.</span>
            </a>
            <button onclick="closeMobileDrawer()" style="width: 36px; height: 36px; border-radius: 8px; border: none; background: #f1f5f9; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 18px; height: 18px; color: #64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <nav style="padding: 16px;">
            <a href="{{ route('admin.today') }}" class="{{ request()->routeIs('admin.today') ? 'admin-nav-active' : 'admin-nav-item' }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; font-size: 15px; font-weight: 500; text-decoration: none; margin-bottom: 4px;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span>Today's Tasks</span>
            </a>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'admin-nav-active' : 'admin-nav-item' }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; font-size: 15px; font-weight: 500; text-decoration: none; margin-bottom: 4px;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dashboard</span>
            </a>
            <div style="margin-top: 20px; margin-bottom: 8px; padding: 0 12px;">
                <span style="font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.2em; color: #cbd5e1;">Scheduler</span>
            </div>
            <a href="{{ route('admin.scheduler.working-days') }}" class="{{ request()->routeIs('admin.scheduler.working-days') ? 'admin-nav-active' : 'admin-nav-item' }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; font-size: 15px; font-weight: 500; text-decoration: none; margin-bottom: 4px;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Working Days</span>
            </a>
            <a href="{{ route('admin.scheduler.time-slots', \App\Models\WorkingDay::orderBy('day_number')->first() ?? 1) }}" class="{{ request()->routeIs('admin.scheduler.time-slots') ? 'admin-nav-active' : 'admin-nav-item' }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; font-size: 15px; font-weight: 500; text-decoration: none; margin-bottom: 4px;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Time Slots</span>
            </a>
            <a href="{{ route('admin.scheduler.calendar') }}" class="{{ request()->routeIs('admin.scheduler.calendar*') ? 'admin-nav-active' : 'admin-nav-item' }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; font-size: 15px; font-weight: 500; text-decoration: none; margin-bottom: 4px;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Calendar</span>
            </a>
        </nav>
        <div style="padding: 16px; border-top: 1px solid #e5e7eb; margin-top: auto;">
            <div style="display: flex; align-items: center; gap: 12px; padding: 8px 12px; margin-bottom: 8px;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: #fef8ec; display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 12px; font-weight: 700; color: #92700c;">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <p style="font-size: 14px; font-weight: 500; color: #1e293b; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ auth()->user()->name }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="admin-nav-item" style="width: 100%; display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; font-size: 15px; border: none; background: none; cursor: pointer;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </div>

    <div class="min-h-screen flex">

        <!-- ─── Desktop Sidebar ─── -->
        <aside class="hidden lg:flex flex-col w-64 admin-sidebar" style="position: sticky; top: 0; height: 100vh;">
            <div class="h-16 flex items-center px-6" style="border-bottom: 1px solid #e5e7eb;">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2" style="text-decoration: none;">
                    <span style="font-family: 'Space Grotesk', sans-serif; font-size: 18px; font-weight: 700; color: #1e293b;">Abhiram</span>
                    <span style="font-family: 'Space Grotesk', sans-serif; font-size: 18px; font-weight: 700; color: #d0ad5d;">.</span>
                    <span style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.15em; color: #94a3b8; margin-left: 4px; margin-top: 2px;">Admin</span>
                </a>
            </div>
            <nav style="flex: 1; padding: 24px 16px; overflow-y: auto;">
                <a href="{{ route('admin.today') }}" class="{{ request()->routeIs('admin.today') ? 'admin-nav-active' : 'admin-nav-item' }}" style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; margin-bottom: 4px; transition: all 0.15s;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>Today's Tasks</span>
                </a>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'admin-nav-active' : 'admin-nav-item' }}" style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; margin-bottom: 4px; transition: all 0.15s;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>
                <div style="margin-top: 24px; margin-bottom: 8px; padding: 0 12px;">
                    <span style="font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.2em; color: #cbd5e1;">Scheduler</span>
                </div>
                <a href="{{ route('admin.scheduler.working-days') }}" class="{{ request()->routeIs('admin.scheduler.working-days') ? 'admin-nav-active' : 'admin-nav-item' }}" style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; margin-bottom: 4px; transition: all 0.15s;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Working Days</span>
                </a>
                <a href="{{ route('admin.scheduler.time-slots', \App\Models\WorkingDay::orderBy('day_number')->first() ?? 1) }}" class="{{ request()->routeIs('admin.scheduler.time-slots') ? 'admin-nav-active' : 'admin-nav-item' }}" style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; margin-bottom: 4px; transition: all 0.15s;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Time Slots</span>
                </a>
                <a href="{{ route('admin.scheduler.calendar') }}" class="{{ request()->routeIs('admin.scheduler.calendar*') ? 'admin-nav-active' : 'admin-nav-item' }}" style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; margin-bottom: 4px; transition: all 0.15s;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Calendar</span>
                </a>
            </nav>
            <div style="padding: 16px; border-top: 1px solid #e5e7eb;">
                <div style="display: flex; align-items: center; gap: 12px; padding: 8px 12px;">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #fef8ec; display: flex; align-items: center; justify-content: center;">
                        <span style="font-size: 12px; font-weight: 700; color: #92700c;">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-size: 14px; font-weight: 500; color: #1e293b; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ auth()->user()->name }}</p>
                        <p style="font-size: 12px; color: #94a3b8; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}" style="margin-top: 8px;">
                    @csrf
                    <button type="submit" class="admin-nav-item" style="width: 100%; display: flex; align-items: center; gap: 12px; padding: 8px 12px; border-radius: 8px; font-size: 14px; border: none; background: none; cursor: pointer; transition: all 0.15s;">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ─── Main Content ─── -->
        <div style="flex: 1; display: flex; flex-direction: column; min-width: 0;">

            <!-- Top Bar -->
            <header class="admin-topbar" style="height: 56px; display: flex; align-items: center; justify-content: space-between; padding: 0 16px; position: sticky; top: 0; z-index: 20;">
                <button onclick="openMobileDrawer()" class="lg:hidden" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: none; background: none; cursor: pointer;">
                    <svg style="width: 22px; height: 22px; color: #475569;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 15px; font-weight: 600; color: #475569; margin: 0; flex: 1; text-align: center;">@yield('title', 'Dashboard')</h1>

                <a href="/" target="_blank" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px; text-decoration: none; color: #94a3b8;">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </header>

            <!-- Content -->
            <main class="admin-main-content" style="flex: 1; padding: 16px; overflow-y: auto;">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- ─── Mobile Bottom Navigation ─── -->
    <nav class="mobile-bottom-nav">
        @php
            $bottomNavItems = [
                ['route' => 'admin.today', 'label' => 'Tasks', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                ['route' => 'admin.dashboard', 'label' => 'Home', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['route' => 'admin.scheduler.calendar', 'label' => 'Calendar', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ];
        @endphp
        <div style="display: flex; width: 100%; justify-content: space-around; padding: 8px 0 4px;">
            @foreach($bottomNavItems as $item)
                @php $isActive = request()->routeIs($item['route'] . '*'); @endphp
                <a href="{{ route($item['route']) }}" style="display: flex; flex-direction: column; align-items: center; gap: 2px; padding: 6px 12px; text-decoration: none; min-width: 64px;">
                    <svg style="width: 24px; height: 24px; color: {{ $isActive ? '#d0ad5d' : '#94a3b8' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isActive ? '2' : '1.5' }}" d="{{ $item['icon'] }}"/>
                    </svg>
                    <span style="font-size: 10px; font-weight: {{ $isActive ? '600' : '500' }}; color: {{ $isActive ? '#d0ad5d' : '#94a3b8' }};">{{ $item['label'] }}</span>
                </a>
            @endforeach
            <button onclick="openMobileDrawer()" style="display: flex; flex-direction: column; align-items: center; gap: 2px; padding: 6px 12px; background: none; border: none; cursor: pointer; min-width: 64px;">
                <svg style="width: 24px; height: 24px; color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <span style="font-size: 10px; font-weight: 500; color: #94a3b8;">More</span>
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
