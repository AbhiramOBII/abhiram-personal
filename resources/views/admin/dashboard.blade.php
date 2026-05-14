@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div style="max-width: 960px;">

    <!-- Page Header -->
    <div style="margin-bottom: 32px;">
        <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 28px; font-weight: 700; color: #1e293b; margin: 0;">Dashboard</h1>
        <p style="margin-top: 8px; font-size: 14px; color: #94a3b8;">Welcome back, {{ auth()->user()->name }}.</p>
    </div>

    <!-- Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div class="admin-card" style="padding: 24px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #fef8ec; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 20px; height: 20px; color: #d0ad5d;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <span class="admin-badge">Site</span>
            </div>
            <div style="font-family: 'Space Grotesk', sans-serif; font-size: 24px; font-weight: 700; color: #1e293b;">Live</div>
            <p style="margin-top: 4px; font-size: 12px; color: #94a3b8;">Website status</p>
        </div>

        <div class="admin-card" style="padding: 24px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #eff6ff; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 20px; height: 20px; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <span class="admin-badge">Admin</span>
            </div>
            <div style="font-family: 'Space Grotesk', sans-serif; font-size: 24px; font-weight: 700; color: #1e293b;">{{ auth()->user()->email }}</div>
            <p style="margin-top: 4px; font-size: 12px; color: #94a3b8;">Logged in as</p>
        </div>

        <div class="admin-card" style="padding: 24px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #f0fdf4; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 20px; height: 20px; color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="admin-badge">System</span>
            </div>
            <div style="font-family: 'Space Grotesk', sans-serif; font-size: 24px; font-weight: 700; color: #1e293b;">Healthy</div>
            <p style="margin-top: 4px; font-size: 12px; color: #94a3b8;">All systems operational</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="admin-card" style="padding: 28px;">
        <h2 style="font-family: 'Space Grotesk', sans-serif; font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 12px 0;">Quick Actions</h2>
        <p style="font-size: 14px; color: #94a3b8; margin: 0;">Admin features will be added here as we build them out. Use this dashboard as your central hub.</p>
    </div>

</div>
@endsection
