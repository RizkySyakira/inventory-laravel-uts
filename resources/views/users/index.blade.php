@extends('layouts.app')

@section('title', 'Manajemen User - Inventory Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Manajemen User & Role</h1>
        <p class="page-subtitle">Kelola akun pengguna, hak akses Administrator, dan Staff.</p>
    </div>
    <div>
        <a href="{{ route('users.create') }}" class="btn">
            + Tambah User
        </a>
    </div>
</div>

<div class="card">
    <form method="GET" action="{{ route('users.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; align-items: flex-end;">
        <div style="flex: 1; min-width: 200px;">
            <label class="form-label" for="search">Cari User</label>
            <input type="text" id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari nama, username, atau email...">
        </div>

        <div style="width: 160px;">
            <label class="form-label" for="role">Role</label>
            <select id="role" name="role" class="form-control">
                <option value="">Semua Role</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
            </select>
        </div>

        <div>
            <button type="submit" class="btn">Filter</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Dibuat Pada</th>
                <th style="width: 150px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $users->firstItem() + $loop->index }}</td>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td><code>{{ $user->username ?? '-' }}</code></td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge {{ $user->role === 'admin' ? 'badge-success' : ($user->role === 'staff' ? 'badge-info' : 'badge-warning') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>{{ $user->created_at?->format('d-m-Y H:i') ?? '-' }}</td>
                    <td>
                        <div style="display: flex; gap: 6px;">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">
                                Edit
                            </a>

                            @if (auth()->id() !== $user->id)
                                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #64748b; padding: 24px;">
                        Tidak ada data user ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-container">
        {{ $users->links() }}
    </div>
</div>
@endsection
