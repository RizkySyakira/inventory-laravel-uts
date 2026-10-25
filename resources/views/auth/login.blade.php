<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Petugas & Akun - Inventory Store</title>

    <style>
        :root {
            --primary: #0f766e;
            --primary-hover: #115e59;
            --navy-dark: #0f172a;
            --slate-bg: #f8fafc;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-card {
            background: var(--card-bg);
            width: 100%;
            max-width: 440px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
            padding: 36px;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand-title {
            font-size: 22px;
            font-weight: 800;
            color: var(--navy-dark);
            letter-spacing: -0.02em;
        }

        .brand-subtitle {
            font-size: 13.5px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-size: 13.5px;
            font-weight: 600;
            color: #334155;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            font-size: 14px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            transition: all 0.15s ease-in-out;
            background-color: #ffffff;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.18);
        }

        .btn-submit {
            width: 100%;
            padding: 11px;
            background-color: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-size: 14.5px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.15s ease;
            margin-top: 8px;
        }

        .btn-submit:hover {
            background-color: var(--primary-hover);
        }

        .alert-error {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            color: #991b1b;
            padding: 12px 14px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #ecfdf5;
            border-left: 4px solid #10b981;
            color: #065f46;
            padding: 12px 14px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .demo-accounts {
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px dashed var(--border-color);
            font-size: 12px;
            color: var(--text-muted);
        }

        .demo-pill {
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 4px;
            font-family: monospace;
            color: #0f172a;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            font-size: 13px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="brand-header">
            <h1 class="brand-title">INVENTORY STORE</h1>
            <p class="brand-subtitle">Silakan masuk menggunakan akun petugas atau pengguna</p>
        </div>

        @if (session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="username" class="form-label">Username atau Alamat Email</label>
                <input type="text"
                       id="username"
                       name="username"
                       class="form-control"
                       value="{{ old('username') }}"
                       placeholder="Contoh: admin atau staff"
                       required
                       autofocus>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Kata Sandi</label>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control"
                       placeholder="Masukkan kata sandi..."
                       autocomplete="current-password"
                       required>
            </div>

            <button type="submit" class="btn-submit">
                Masuk ke Sistem
            </button>
        </form>

        <a href="{{ route('catalog.index') }}" class="back-link">
            &larr; Kembali ke Katalog Produk
        </a>

        <div class="demo-accounts">
            <div style="font-weight: 700; margin-bottom: 6px; color: #334155;">Akun Pengujian Sistem:</div>
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <div>Admin: <span class="demo-pill">admin</span> / <span class="demo-pill">admin123</span></div>
                <div>Staff: <span class="demo-pill">staff</span> / <span class="demo-pill">staff123</span></div>
                <div>Customer: <span class="demo-pill">customer</span> / <span class="demo-pill">customer123</span></div>
            </div>
        </div>
    </div>

</body>
</html>
