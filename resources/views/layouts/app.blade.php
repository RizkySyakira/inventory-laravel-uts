<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Manajemen Persediaan & Penjualan') - Inventory Store</title>

    <style>
        :root {
            --primary: #0f766e;
            --primary-hover: #115e59;
            --primary-light: #f0fdfa;
            --navy-dark: #0f172a;
            --navy-surface: #1e293b;
            --navy-border: #334155;
            --slate-bg: #f8fafc;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --success: #059669;
            --success-bg: #ecfdf5;
            --danger: #dc2626;
            --danger-bg: #fef2f2;
            --warning: #d97706;
            --warning-bg: #fffbeb;
            --info: #0284c7;
            --info-bg: #f0f9ff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--slate-bg);
            color: var(--text-main);
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Bar & Navbar */
        .app-navbar {
            background-color: var(--navy-dark);
            color: #ffffff;
            border-bottom: 2px solid var(--primary);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
        }

        .navbar-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 64px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #ffffff;
            font-weight: 700;
            font-size: 18px;
            letter-spacing: -0.02em;
        }

        .nav-brand-tag {
            background: var(--primary);
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 2px 7px;
            border-radius: 4px;
            letter-spacing: 0.05em;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-wrap: wrap;
        }

        .nav-link {
            color: #cbd5e1;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            padding: 7px 12px;
            border-radius: 6px;
            transition: all 0.15s ease-in-out;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.08);
        }

        .nav-link.active {
            color: #ffffff;
            background-color: var(--primary);
            font-weight: 600;
        }

        .nav-link-highlight {
            background-color: rgba(15, 118, 110, 0.25);
            color: #5eead4;
            border: 1px solid rgba(94, 234, 212, 0.3);
        }

        .nav-link-highlight:hover {
            background-color: var(--primary);
            color: #ffffff;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .user-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 12px;
            background-color: var(--navy-surface);
            border: 1px solid var(--navy-border);
            border-radius: 20px;
            font-size: 13px;
        }

        .user-name {
            font-weight: 600;
            color: #f1f5f9;
        }

        .role-badge {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 2px 8px;
            border-radius: 12px;
            letter-spacing: 0.03em;
        }

        .role-badge.admin {
            background-color: #e0e7ff;
            color: #3730a3;
        }

        .role-badge.staff {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .role-badge.customer {
            background-color: #dcfce7;
            color: #166534;
        }

        .btn-nav-logout {
            background: #ef4444;
            color: #ffffff;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.15s;
        }

        .btn-nav-logout:hover {
            background: #dc2626;
        }

        .btn-nav-login {
            background: var(--primary);
            color: #ffffff;
            text-decoration: none;
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            transition: background-color 0.15s;
        }

        .btn-nav-login:hover {
            background: var(--primary-hover);
        }

        /* Layout Container */
        .main-content {
            flex: 1;
            max-width: 1280px;
            width: 100%;
            margin: 28px auto;
            padding: 0 20px;
        }

        /* Page Headers */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 16px;
        }

        .page-header-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.01em;
        }

        .page-header-subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .page-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* Cards */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--border-color);
        }

        .card-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--text-main);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            line-height: 1.4;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .btn-secondary {
            background-color: #64748b;
            color: #ffffff;
            border-color: #64748b;
        }

        .btn-secondary:hover {
            background-color: #475569;
            border-color: #475569;
        }

        .btn-outline {
            background-color: #ffffff;
            color: var(--text-main);
            border-color: var(--border-color);
        }

        .btn-outline:hover {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
        }

        .btn-danger {
            background-color: var(--danger);
            color: #ffffff;
            border-color: var(--danger);
        }

        .btn-danger:hover {
            background-color: #b91c1c;
            border-color: #b91c1c;
        }

        .btn-warning {
            background-color: var(--warning);
            color: #ffffff;
            border-color: var(--warning);
        }

        .btn-warning:hover {
            background-color: #b45309;
            border-color: #b45309;
        }

        .btn-success {
            background-color: var(--success);
            color: #ffffff;
            border-color: var(--success);
        }

        .btn-success:hover {
            background-color: #047857;
            border-color: #047857;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12.5px;
            border-radius: 5px;
        }

        .btn-lg {
            padding: 12px 24px;
            font-size: 16px;
            border-radius: 8px;
        }

        /* Alerts */
        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 22px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            border-left: 4px solid;
        }

        .alert-success {
            background-color: var(--success-bg);
            border-color: var(--success);
            color: #065f46;
        }

        .alert-danger {
            background-color: var(--danger-bg);
            border-color: var(--danger);
            color: #991b1b;
        }

        .alert-warning {
            background-color: var(--warning-bg);
            border-color: var(--warning);
            color: #92400e;
        }

        .alert-info {
            background-color: var(--info-bg);
            border-color: var(--info);
            color: #075985;
        }

        .alert ul {
            margin: 4px 0 0 20px;
            padding: 0;
        }

        /* Forms */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 13.5px;
            color: #334155;
        }

        .form-control {
            width: 100%;
            padding: 9px 13px;
            font-size: 14px;
            line-height: 1.5;
            color: var(--text-main);
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.18);
        }

        .form-text {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Tables */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
            background: #ffffff;
        }

        th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11.5px;
            letter-spacing: 0.04em;
            padding: 12px 14px;
            border-bottom: 1px solid var(--border-color);
            text-align: left;
        }

        td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border-color);
            color: #1e293b;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            font-size: 11.5px;
            font-weight: 600;
            border-radius: 6px;
            line-height: 1.3;
        }

        .badge-success { background: var(--success-bg); color: var(--success); }
        .badge-danger  { background: var(--danger-bg);  color: var(--danger); }
        .badge-warning { background: var(--warning-bg); color: var(--warning); }
        .badge-info    { background: var(--info-bg);    color: var(--info); }
        .badge-secondary { background: #f1f5f9; color: #475569; }

        /* Footer */
        .app-footer {
            background-color: #ffffff;
            border-top: 1px solid var(--border-color);
            padding: 24px 20px;
            margin-top: auto;
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
        }

        .footer-container {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        @media (max-width: 768px) {
            .navbar-container {
                flex-direction: column;
                align-items: flex-start;
                padding: 12px 16px;
            }
            .nav-menu {
                width: 100%;
                overflow-x: auto;
                padding-bottom: 6px;
            }
            .nav-actions {
                width: 100%;
                justify-content: space-between;
            }
            .footer-container {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <nav class="app-navbar">
        <div class="navbar-container">
            <a href="{{ auth()->check() && auth()->user()->role !== 'customer' ? route('dashboard') : route('catalog.index') }}" class="nav-brand">
                <span>INVENTORY STORE</span>
                <span class="nav-brand-tag">POS & Persediaan</span>
            </a>

            <div class="nav-menu">
                @auth
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                        <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">Produk</a>
                        <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">Kategori</a>
                        <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">Supplier</a>
                        <a href="{{ route('purchases.index') }}" class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}">Pembelian</a>
                        <a href="{{ route('sales.index') }}" class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">Penjualan</a>
                        <a href="{{ route('stock-movements.index') }}" class="nav-link {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}">Mutasi Stok</a>
                        <a href="{{ route('stock-reports.index') }}" class="nav-link {{ request()->routeIs('stock-reports.*') ? 'active' : '' }}">Laporan Stok</a>
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">Pengguna</a>
                        @endif
                        <a href="{{ route('catalog.index') }}" class="nav-link nav-link-highlight" target="_blank">Lihat Katalog</a>
                    @else
                        {{-- Logged in as Customer --}}
                        <a href="{{ route('catalog.index') }}" class="nav-link {{ request()->routeIs('catalog.index') || request()->routeIs('catalog.show') ? 'active' : '' }}">Katalog Produk</a>
                    @endif
                @else
                    {{-- Guest Visitor --}}
                    <a href="{{ route('catalog.index') }}" class="nav-link {{ request()->routeIs('catalog.index') || request()->routeIs('catalog.show') ? 'active' : '' }}">Katalog Produk</a>
                @endauth
            </div>

            <div class="nav-actions">
                @auth
                    <div class="user-pill">
                        <span class="user-name">{{ auth()->user()->name }}</span>
                        <span class="role-badge {{ auth()->user()->role }}">{{ ucfirst(auth()->user()->role) }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-nav-logout">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-nav-login">Login Petugas / Akun</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="main-content">
        @if (session('success'))
            <div class="alert alert-success">
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning">
                <div>{{ session('warning') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Terjadi beberapa kesalahan data:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="app-footer">
        <div class="footer-container">
            <div>
                <strong>Inventory Store Indonesia</strong> &mdash; Sistem Manajemen Persediaan, Kasir Toko, dan Katalog Produk.
            </div>
            <div>
                Transparansi Stok &bull; Layanan Kasir Cepat &bull; Struk Resmi
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
