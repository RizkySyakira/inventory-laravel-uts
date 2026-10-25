<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Menampilkan katalog produk publik untuk customer.
     * Customer hanya dapat melihat produk dan stok, transaksi dilakukan offline di kasir.
     */
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();

        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $sort = $request->input('sort', 'name_asc');
        match ($sort) {
            'price_low' => $query->orderBy('selling_price', 'asc'),
            'price_high' => $query->orderBy('selling_price', 'desc'),
            'stock_high' => $query->orderBy('stock', 'desc'),
            default => $query->orderBy('name', 'asc'),
        };

        $products = $query->paginate(12)->withQueryString();

        return view('catalog.index', compact('products', 'categories'));
    }

    /**
     * Detail produk untuk customer.
     */
    public function show(Product $product)
    {
        $product->load('category');

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('catalog.show', compact('product', 'relatedProducts'));
    }

    /**
     * Tampilan struk pembayaran ramah cetak.
     */
    public function receipt(string $saleNumber)
    {
        $sale = Sale::with(['items.product', 'user'])
            ->where('sale_number', $saleNumber)
            ->firstOrFail();

        return view('sales.receipt', compact('sale'));
    }
}
