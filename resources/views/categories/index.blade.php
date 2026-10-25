@extends('layouts.app')

@section('title', 'Data Kategori Produk')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header-title">Data Kategori Produk</h1>
        <p class="page-header-subtitle">Pengelompokan barang inventaris dan etalase toko.</p>
    </div>
    <div class="page-header-actions">
        @if (auth()->user()->role === 'admin')
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                + Tambah Kategori
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
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th style="text-align: center; width: 140px;">Jumlah Produk</th>
                    <th style="text-align: center; width: 180px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>{{ $categories->firstItem() + $loop->index }}</td>
                        <td>
                            <strong style="color: #0f172a;">{{ $category->name }}</strong>
                        </td>
                        <td style="color: #64748b;">
                            {{ $category->description ?: '-' }}
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-info">{{ $category->products_count }} produk</span>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: inline-flex; gap: 4px;">
                                <a href="{{ route('categories.show', $category) }}" class="btn btn-outline btn-sm">
                                    Detail
                                </a>

                                @if (auth()->user()->role === 'admin')
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('categories.destroy', $category) }}"
                                          method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')"
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
                        <td colspan="5" style="text-align: center; padding: 32px; color: var(--text-muted);">
                            Belum ada data kategori produk.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $categories->links() }}
    </div>
</div>

@endsection
