@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('sidebar')
<nav>
    <a href="{{ route('dashboard') }}" class="nav-item active">
        📊 Dashboard
    </a>
    <a href="#" class="nav-item">
        👥 Staff Management
    </a>
    <a href="#" class="nav-item">
        🍽️ Menu Management
    </a>
    <a href="#" class="nav-item">
        📦 Inventory
    </a>
    <a href="#" class="nav-item">
        💰 Sales Reports
    </a>
    <a href="#" class="nav-item">
        📋 Orders
    </a>
    <a href="#" class="nav-item">
        ⚙️ Settings
    </a>
</nav>
@endsection

@section('content')
<div class="page-title">Admin Dashboard</div>
<div class="page-subtitle">Welcome back, {{ $user->name }}! Here's your restaurant overview.</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value">KSh {{ number_format($stats['total_sales']) }}</div>
        <div class="stat-label">Total Sales Today</div>
    </div>
    <div class="stat-card success">
        <div class="stat-value">{{ $stats['total_orders'] }}</div>
        <div class="stat-label">Orders Today</div>
    </div>
    <div class="stat-card warning">
        <div class="stat-value">{{ $stats['active_staff'] }}</div>
        <div class="stat-label">Active Staff</div>
    </div>
    <div class="stat-card danger">
        <div class="stat-value">{{ $stats['inventory_items'] }}</div>
        <div class="stat-label">Inventory Items</div>
    </div>
</div>

<!-- Quick Actions -->
<div class="content-section">
    <div class="section-title">Quick Actions</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem">
        <button style="background:#3b82f6;color:#fff;border:none;padding:1rem;border-radius:0.5rem;cursor:pointer;font-weight:500">
            + Add New Staff
        </button>
        <button style="background:#10b981;color:#fff;border:none;padding:1rem;border-radius:0.5rem;cursor:pointer;font-weight:500">
            + Add Menu Item
        </button>
        <button style="background:#f59e0b;color:#fff;border:none;padding:1rem;border-radius:0.5rem;cursor:pointer;font-weight:500">
            📊 View Reports
        </button>
        <button style="background:#8b5cf6;color:#fff;border:none;padding:1rem;border-radius:0.5rem;cursor:pointer;font-weight:500">
            ⚙️ System Settings
        </button>
    </div>
</div>

<!-- Recent Activity -->
<div class="content-section">
    <div class="section-title">Recent Activity</div>
    <div style="color:#64748b;text-align:center;padding:2rem">
        <p>No recent activity to display.</p>
        <p style="font-size:0.875rem;margin-top:0.5rem">Activity will appear here as you use the system.</p>
    </div>
</div>

<!-- System Overview -->
<div class="content-section">
    <div class="section-title">System Overview</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1rem">
        <div style="padding:1rem;background:#f8fafc;border-radius:0.375rem">
            <div style="font-weight:600;color:#1e293b;margin-bottom:0.5rem">Restaurant Status</div>
            <div style="color:#10b981;font-size:0.875rem">🟢 Online & Operating</div>
        </div>
        <div style="padding:1rem;background:#f8fafc;border-radius:0.375rem">
            <div style="font-weight:600;color:#1e293b;margin-bottom:0.5rem">Payment System</div>
            <div style="color:#10b981;font-size:0.875rem">🟢 M-Pesa Connected</div>
        </div>
        <div style="padding:1rem;background:#f8fafc;border-radius:0.375rem">
            <div style="font-weight:600;color:#1e293b;margin-bottom:0.5rem">Kitchen Display</div>
            <div style="color:#f59e0b;font-size:0.875rem">🟡 Pending Setup</div>
        </div>
    </div>
</div>
@endsection