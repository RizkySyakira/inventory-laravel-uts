@extends('layouts.app')

@section('title', 'Data Supplier & Pemasok')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header-title">Data Supplier & Pemasok</h1>
        <p class="page-header-subtitle">Daftar distributor resmi dan mitra pengadaan barang inventaris toko.</p>
    </div>
    <div class="page-header-actions">
        @if (auth()->user()->role === 'admin')
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                + Tambah Supplier Baru
            </a>
        @endif
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Nama Supplier</th>
                    <th>Kontak Person</th>
                    <th>Nomor Telepon</th>
                    <th>Alamat</th>
                    <th style="text-align: center; width: 180px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td>{{ $suppliers->firstItem() + $loop->index }}</td>
                        <td>
                            <strong style="color: #0f172a;">{{ $supplier->name }}</strong>
                        </td>
                        <td>{{ $supplier->contact_name ?? '-' }}</td>
                        <td>
                            <code>{{ $supplier->phone ?? '-' }}</code>
                        </td>
                        <td style="color: #64748b;">{{ $supplier->address ?? '-' }}</td>
                        <td style="text-align: center;">
                            <div style="display: inline-flex; gap: 4px;">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline btn-sm">
                                    Detail
                                </a>

                                @if (auth()->user()->role === 'admin')
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('suppliers.destroy', $supplier) }}"
                                          method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?')"
                                          style="margin: 0;">
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
                        <td colspan="6" style="text-align: center; padding: 32px; color: var(--text-muted);">
                            Belum ada data supplier pemasok.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $suppliers->links() }}
    </div>
</div>

@endsection
