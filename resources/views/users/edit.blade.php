@extends('layouts.app')

@section('title', 'Edit User - Inventory Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit User: {{ $user->name }}</h1>
        <p class="page-subtitle">Perbarui profil pengguna, role, atau ganti password.</p>
    </div>
    <div>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>
</div>

<div class="card" style="max-width: 650px;">
    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label" for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Alamat Email</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password Baru (Opsional)</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
            <div class="form-hint">Isi hanya jika ingin mengganti password user ini.</div>
        </div>

        <div class="form-group">
            <label class="form-label" for="role">Hak Akses (Role)</label>
            <select id="role" name="role" class="form-control" required {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff</option>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin / Owner</option>
                <option value="customer" {{ old('role', $user->role) === 'customer' ? 'selected' : '' }}>Customer</option>
            </select>
            @if(auth()->id() === $user->id)
                <input type="hidden" name="role" value="{{ $user->role }}">
                <div class="form-hint">Anda tidak dapat mengubah role akun yang sedang Anda gunakan.</div>
            @endif
        </div>

        <div style="margin-top: 24px; display: flex; gap: 10px;">
            <button type="submit" class="btn">
                Simpan Perubahan
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
