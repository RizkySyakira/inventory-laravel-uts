@extends('layouts.app')

@section('title', 'Tambah User - Inventory Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Tambah User Baru</h1>
        <p class="page-subtitle">Buat akun baru dan tentukan hak akses sebagai Admin atau Staff.</p>
    </div>
    <div>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>
</div>

<div class="card" style="max-width: 650px;">
    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label" for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Nayla Salsabila" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" value="{{ old('username') }}" placeholder="Contoh: nayla123" required>
            <div class="form-hint">Digunakan saat login. Karakter alfanumerik tanpa spasi.</div>
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Alamat Email</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Contoh: nayla@example.com" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="role">Hak Akses (Role)</label>
            <select id="role" name="role" class="form-control" required>
                <option value="staff" {{ old('role', 'staff') === 'staff' ? 'selected' : '' }}>Staff (Hanya Transaksi & Melihat Data)</option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin / Owner (Akses Penuh Master Data & Manajemen User)</option>
                <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer (Pelanggan Toko Online)</option>
            </select>
        </div>

        <div style="margin-top: 24px; display: flex; gap: 10px;">
            <button type="submit" class="btn">
                Simpan User
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
