<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CustomerShopController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();

        $query = Product::with('category')
            ->where('stock', '>', 0); // Customer hanya melihat produk yang ada stoknya

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

        // Sorting
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');

        if ($sort === 'price_low') {
            $query->orderBy('selling_price', 'asc');
        } elseif ($sort === 'price_high') {
            $query->orderBy('selling_price', 'desc');
        } else {
            $query->orderBy('name', 'asc');
        }

        $products = $query->paginate(12)->withQueryString();

        return view('customer.shop', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        $product->load('category');

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('stock', '>', 0)
            ->take(4)
            ->get();

        return view('customer.show', compact('product', 'relatedProducts'));
    }
}
