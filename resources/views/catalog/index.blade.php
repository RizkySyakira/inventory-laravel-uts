@extends('layouts.app')

@section('title', 'Katalog Produk - Inventory Store')

@section('styles')
<style>
    .catalog-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        border-radius: 12px;
        padding: 32px;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        border: 1px solid #334155;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12);
    }

    .catalog-hero-content {
        max-width: 680px;
    }

    .catalog-hero-title {
        font-size: 28px;
        font-weight: 800;
        margin-bottom: 8px;
        letter-spacing: -0.02em;
        color: #ffffff;
    }

    .catalog-hero-desc {
        font-size: 15px;
        color: #cbd5e1;
        line-height: 1.6;
    }

    .hero-banner-img {
        max-width: 260px;
        width: 100%;
        height: auto;
        border-radius: 8px;
        display: block;
    }

    .filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 24px;
    }

    .filter-form {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    .filter-group {
        flex: 1;
        min-width: 180px;
    }

    .category-pills {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid #f1f5f9;
    }

    .category-pill {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        background: #f1f5f9;
        color: #475569;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.15s ease-in-out;
        border: 1px solid transparent;
    }

    .category-pill:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .category-pill.active {
        background: #0f766e;
        color: #ffffff;
        border-color: #0f766e;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
        gap: 22px;
        margin-bottom: 32px;
    }

    .product-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-top: 3px solid #0f766e;
        border-radius: 10px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.08);
        border-color: #cbd5e1;
    }

    .product-img-wrapper {
        width: 100%;
        height: 200px;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        padding: 16px;
    }

    .product-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .product-content {
        padding: 18px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .product-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .product-category {
        font-size: 12px;
        font-weight: 700;
        color: #0f766e;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .product-sku {
        font-size: 11.5px;
        color: #94a3b8;
        font-family: monospace;
    }

    .product-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
        line-height: 1.35;
        text-decoration: none;
    }

    .product-title:hover {
        color: #0f766e;
    }

    .product-desc {
        font-size: 13px;
        color: #64748b;
        margin-bottom: 14px;
        line-height: 1.45;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-footer {
        margin-top: auto;
        padding-top: 14px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .product-price {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }

    .product-price-label {
        font-size: 11px;
        color: #64748b;
        display: block;
        font-weight: 500;
    }

    .offline-purchase-badge {
        font-size: 11px;
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 4px 8px;
        border-radius: 4px;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .catalog-hero {
            flex-direction: column;
            align-items: flex-start;
            padding: 24px;
        }
        .hero-banner-img {
            max-width: 100%;
        }
    }
</style>
@endsection

@section('content')

    {{-- Hero Section --}}
    <div class="catalog-hero">
        <div class="catalog-hero-content">
            <h1 class="catalog-hero-title">Katalog Produk</h1>
            <p class="catalog-hero-desc">
                Temukan berbagai pilihan perlengkapan kantor, alat tulis, dan aksesoris komputer berkualitas dengan harga terbaik dan ketersediaan stok yang selalu terbarui.
            </p>
        </div>
        <img src="{{ asset('images/brand/store-banner.svg') }}" alt="Inventory Store Banner" class="hero-banner-img">
    </div>

    {{-- Filter & Search Form --}}
    <div class="filter-card">
        <form action="{{ route('catalog.index') }}" method="GET" class="filter-form">
            <div class="filter-group" style="flex: 2;">
                <label class="form-label" for="search">Cari Nama Produk / SKU</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Contoh: Wireless Mouse, Kertas HVS..." value="{{ request('search') }}">
            </div>

            <div class="filter-group">
                <label class="form-label" for="category_id">Kategori</label>
                <select name="category_id" id="category_id" class="form-control">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="form-label" for="sort">Urutan</label>
                <select name="sort" id="sort" class="form-control">
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama (A - Z)</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                    <option value="stock_high" {{ request('sort') == 'stock_high' ? 'selected' : '' }}>Stok Terbanyak</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn-primary">Filter Produk</button>
                @if(request()->anyFilled(['search', 'category_id', 'sort']))
                    <a href="{{ route('catalog.index') }}" class="btn btn-outline">Reset</a>
                @endif
            </div>
        </form>

        {{-- Category Pills --}}
        <div class="category-pills">
            <a href="{{ route('catalog.index') }}" class="category-pill {{ !request('category_id') ? 'active' : '' }}">
                Semua Kategori
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('catalog.index', array_merge(request()->query(), ['category_id' => $cat->id])) }}" class="category-pill {{ request('category_id') == $cat->id ? 'active' : '' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Product Grid --}}
    @if($products->isEmpty())
        <div class="card" style="text-align: center; padding: 48px 20px;">
            <h3 style="font-size: 18px; margin-bottom: 8px; color: var(--text-main);">Tidak ada produk yang cocok dengan pencarian</h3>
            <p style="color: var(--text-muted); margin-bottom: 20px;">Silakan coba kata kunci lain atau pilih kategori yang berbeda.</p>
            <a href="{{ route('catalog.index') }}" class="btn btn-primary">Tampilkan Semua Produk</a>
        </div>
    @else
        <div class="products-grid">
            @foreach($products as $product)
                <div class="product-card">
                    <div class="product-content">
                        <div class="product-meta">
                            <span class="product-category">{{ $product->category->name ?? 'Umum' }}</span>
                            <span class="product-sku">{{ $product->sku }}</span>
                        </div>

                        <a href="{{ route('catalog.show', $product->id) }}" class="product-title">
                            {{ $product->name }}
                        </a>

                        <p class="product-desc">
                            {{ $product->description ?: 'Perlengkapan berkualitas standar industri untuk operasional kantor dan bisnis.' }}
                        </p>

                        <div style="margin-bottom: 12px;">
                            @if($product->stock > 10)
                                <span class="badge badge-success">Stok: {{ $product->stock }} (Tersedia)</span>
                            @elseif($product->stock > 0)
                                <span class="badge badge-warning">Stok: {{ $product->stock }} (Tersisa Sedikit)</span>
                            @else
                                <span class="badge badge-danger">Stok Habis</span>
                            @endif
                        </div>

                        <div class="product-footer">
                            <div>
                                <span class="product-price-label">Harga Satuan</span>
                                <span class="product-price">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                            </div>
                            <a href="{{ route('catalog.show', $product->id) }}" class="btn btn-outline btn-sm">Detail Produk</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $products->links() }}
        </div>
    @endif

@endsection
