@extends('layouts.dashboard')

@section('title', 'Cashier Dashboard')

@section('sidebar')
<nav>
    <a href="{{ route('dashboard') }}" class="nav-item active">
        📊 Dashboard
    </a>
    <a href="#" class="nav-item">
        🛒 New Order
    </a>
    <a href="#" class="nav-item">
        📋 Today's Orders
    </a>
    <a href="#" class="nav-item">
        🍽️ Menu
    </a>
    <a href="#" class="nav-item">
        💰 Payments
    </a>
    <a href="#" class="nav-item">
        👥 Customers
    </a>
    <a href="#" class="nav-item">
        📈 My Performance
    </a>
</nav>
@endsection

@section('content')
<div class="page-title">Cashier Dashboard</div>
<div class="page-subtitle">Welcome {{ $user->name }}! Ready to serve customers.</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value">KSh {{ number_format($stats['todays_sales']) }}</div>
        <div class="stat-label">Today's Sales</div>
    </div>
    <div class="stat-card success">
        <div class="stat-value">{{ $stats['orders_processed'] }}</div>
        <div class="stat-label">Orders Processed</div>
    </div>
    <div class="stat-card warning">
        <div class="stat-value">{{ $stats['customers_served'] }}</div>
        <div class="stat-label">Customers Served</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">KSh {{ number_format($stats['average_order']) }}</div>
        <div class="stat-label">Average Order</div>
    </div>
</div>

<!-- Quick Order -->
<div class="content-section">
    <div class="section-title">Start New Order</div>
    <div style="text-align:center;padding:2rem">
        <a href="{{ route('pos.index') }}" style="background:#10b981;color:#fff;border:none;padding:1.5rem 3rem;border-radius:0.5rem;cursor:pointer;font-weight:600;font-size:1.125rem;box-shadow:0 4px 6px rgba(0,0,0,0.1);text-decoration:none;display:inline-block">
            🛒 Open POS System
        </a>
        <p style="color:#64748b;margin-top:1rem;font-size:0.875rem">Click to open the Point of Sale system for M-Pesa transactions</p>
    </div>
</div>

<!-- Cashier Actions -->
<div class="content-section">
    <div class="section-title">Quick Actions</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem">
        <button style="background:#3b82f6;color:#fff;border:none;padding:1rem;border-radius:0.5rem;cursor:pointer;font-weight:500">
            📋 View Today's Orders
        </button>
        <button style="background:#8b5cf6;color:#fff;border:none;padding:1rem;border-radius:0.5rem;cursor:pointer;font-weight:500">
            💰 Process Payment
        </button>
        <button style="background:#f59e0b;color:#fff;border:none;padding:1rem;border-radius:0.5rem;cursor:pointer;font-weight:500">
            👥 Customer Lookup
        </button>
        <button style="background:#ef4444;color:#fff;border:none;padding:1rem;border-radius:0.5rem;cursor:pointer;font-weight:500">
            🔄 Refund/Return
        </button>
    </div>
</div>

<!-- Recent Orders -->
<div class="content-section">
    <div class="section-title">Recent Orders</div>
    <div style="color:#64748b;text-align:center;padding:2rem">
        <p>📋 No orders today</p>
        <p style="font-size:0.875rem;margin-top:0.5rem">Your recent orders will appear here.</p>
    </div>
</div>

<!-- Payment Methods -->
<div class="content-section">
    <div class="section-title">Payment Methods Status</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem">
        <div style="padding:1rem;background:#f0fdf4;border-radius:0.375rem;border:1px solid #bbf7d0">
            <div style="font-weight:600;color:#166534;margin-bottom:0.5rem">💵 Cash</div>
            <div style="color:#16a34a;font-size:0.875rem">✅ Available</div>
        </div>
        <div style="padding:1rem;background:#f0fdf4;border-radius:0.375rem;border:1px solid #bbf7d0">
            <div style="font-weight:600;color:#166534;margin-bottom:0.5rem">📱 M-Pesa</div>
            <div style="color:#16a34a;font-size:0.875rem">✅ Connected</div>
        </div>
        <div style="padding:1rem;background:#fef3c7;border-radius:0.375rem;border:1px solid #fde047">
            <div style="font-weight:600;color:#a16207;margin-bottom:0.5rem">💳 Card Payment</div>
            <div style="color:#ca8a04;font-size:0.875rem">⚠️ Setup Required</div>
        </div>
        <div style="padding:1rem;background:#f0fdf4;border-radius:0.375rem;border:1px solid #bbf7d0">
            <div style="font-weight:600;color:#166534;margin-bottom:0.5rem">🏦 Bank Transfer</div>
            <div style="color:#16a34a;font-size:0.875rem">✅ Available</div>
        </div>
    </div>
</div>

<!-- Performance Summary -->
<div class="content-section">
    <div class="section-title">Your Performance Today</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1rem">
        <div style="padding:1rem;background:#eff6ff;border-radius:0.375rem;border:1px solid #bfdbfe">
            <div style="font-weight:600;color:#1e40af;margin-bottom:0.5rem">Order Speed</div>
            <div style="color:#1e40af;font-size:1.5rem;font-weight:700">2.3 min</div>
            <div style="color:#2563eb;font-size:0.875rem">Average per order</div>
        </div>
        <div style="padding:1rem;background:#f0fdf4;border-radius:0.375rem;border:1px solid #bbf7d0">
            <div style="font-weight:600;color:#166534;margin-bottom:0.5rem">Accuracy Rate</div>
            <div style="color:#166534;font-size:1.5rem;font-weight:700">100%</div>
            <div style="color:#16a34a;font-size:0.875rem">Perfect today!</div>
        </div>
        <div style="padding:1rem;background:#fef3c7;border-radius:0.375rem;border:1px solid #fde047">
            <div style="font-weight:600;color:#a16207;margin-bottom:0.5rem">Customer Rating</div>
            <div style="color:#a16207;font-size:1.5rem;font-weight:700">⭐⭐⭐⭐⭐</div>
            <div style="color:#ca8a04;font-size:0.875rem">Excellent service</div>
        </div>
    </div>
</div>
@endsection