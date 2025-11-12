<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') • Kijabe POS</title>
    <style>
        /* Modern Dashboard CSS */
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:Inter,Segoe UI,Roboto,Arial,sans-serif;background:#f8fafc;color:#334155}
        
        /* Header */
        .header{background:#1e293b;color:#fff;padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center;box-shadow:0 1px 3px rgba(0,0,0,0.1)}
        .logo{font-size:1.5rem;font-weight:700;letter-spacing:1px}
        .user-info{display:flex;align-items:center;gap:1rem}
        .user-name{font-weight:500}
        .user-role{background:#3b82f6;padding:0.25rem 0.75rem;border-radius:1rem;font-size:0.875rem}
        .logout-btn{background:#ef4444;color:#fff;border:none;padding:0.5rem 1rem;border-radius:0.375rem;cursor:pointer;text-decoration:none;font-size:0.875rem}
        
        /* Sidebar */
        .layout{display:flex;min-height:calc(100vh - 80px)}
        .sidebar{width:250px;background:#fff;border-right:1px solid #e2e8f0;padding:1.5rem 0}
        .nav-item{display:block;padding:0.75rem 1.5rem;color:#64748b;text-decoration:none;border-left:3px solid transparent;transition:all 0.2s}
        .nav-item:hover,.nav-item.active{background:#f1f5f9;color:#1e293b;border-left-color:#3b82f6}
        .nav-item.active{background:#eff6ff;color:#1d4ed8;border-left-color:#3b82f6}
        
        /* Main Content */
        .main-content{flex:1;padding:2rem}
        .page-title{font-size:2rem;font-weight:700;color:#1e293b;margin-bottom:0.5rem}
        .page-subtitle{color:#64748b;margin-bottom:2rem}
        
        /* Stats Cards */
        .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1.5rem;margin-bottom:2rem}
        .stat-card{background:#fff;padding:1.5rem;border-radius:0.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.1);border-left:4px solid #3b82f6}
        .stat-card.success{border-left-color:#10b981}
        .stat-card.warning{border-left-color:#f59e0b}
        .stat-card.danger{border-left-color:#ef4444}
        .stat-value{font-size:2rem;font-weight:700;color:#1e293b;margin-bottom:0.25rem}
        .stat-label{color:#64748b;font-size:0.875rem;text-transform:uppercase;letter-spacing:0.05em}
        
        /* Content Sections */
        .content-section{background:#fff;border-radius:0.5rem;padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:1.5rem}
        .section-title{font-size:1.25rem;font-weight:600;color:#1e293b;margin-bottom:1rem}
        
        /* Responsive */
        @media(max-width:768px){
            .header{padding:1rem}
            .layout{flex-direction:column}
            .sidebar{width:100%;order:2}
            .main-content{padding:1rem}
            .stats-grid{grid-template-columns:1fr}
        }
        
        /* Role-specific colors */
        .role-admin .stat-card{border-left-color:#8b5cf6}
        .role-kitchen .stat-card{border-left-color:#10b981}
        .role-cashier .stat-card{border-left-color:#3b82f6}
    </style>
</head>
<body class="role-{{ str_replace('_', '-', $user->role) }}">
    <!-- Header -->
    <header class="header">
        <div class="logo">Kijabe Hospital POS</div>
        <div class="user-info">
            <span class="user-name">{{ $user->name }}</span>
            <span class="user-role">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </header>

    <!-- Main Layout -->
    <div class="layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            @yield('sidebar')
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>
</body>
</html>