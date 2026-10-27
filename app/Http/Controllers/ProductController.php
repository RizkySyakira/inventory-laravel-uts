<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Memastikan hanya Admin yang bisa mengubah data produk.
     */
    private function ensureAdmin(): void
    {
        abort_unless(
            auth()->user()?->role === 'admin',
            403,
            'Akses hanya untuk Admin.'
        );
    }

    /**
     * Menampilkan daftar produk.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $allowedSorts = [
            'name',
            'sku',
            'purchase_price',
            'selling_price',
            'stock',
            'created_at',
        ];

        $sort = $request->query('sort', 'created_at');

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }

        $direction = $request->query('direction', 'desc');

        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        $products = $query
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('products.index', compact(
            'products',
            'categories',
            'sort',
            'direction'
        ));
    }

    /**
     * Menampilkan form tambah produk.
     * Khusus Admin.
     */
    public function create()
    {
        $this->ensureAdmin();

        $categories = Category::orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    /**
     * Menyimpan produk baru.
     * Khusus Admin.
     */
    public function store(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'category_id' => [
                'required',
                'exists:categories,id',
            ],
            'sku' => [
                'required',
                'string',
                'max:50',
                'unique:products,sku',
            ],
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'purchase_price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'selling_price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'stock' => [
                'required',
                'integer',
                'min:0',
            ],
            'minimum_stock' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        Product::create($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail produk.
     */
    public function show(Product $product)
    {
        $product->load('category');

        return view('products.show', compact('product'));
    }

    /**
     * Menampilkan form edit produk.
     * Khusus Admin.
     */
    public function edit(Product $product)
    {
        $this->ensureAdmin();

        $categories = Category::orderBy('name')->get();

        return view('products.edit', compact(
            'product',
            'categories'
        ));
    }

    /**
     * Memperbarui produk.
     * Khusus Admin.
     */
    public function update(Request $request, Product $product)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'category_id' => [
                'required',
                'exists:categories,id',
            ],
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'sku')
                    ->ignore($product->id),
            ],
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'purchase_price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'selling_price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'minimum_stock' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        $product->update($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Menghapus produk.
     * Khusus Admin.
     */
    public function destroy(Product $product)
    {
        $this->ensureAdmin();

        if (
            $product->stockMovements()->exists() ||
            $product->purchaseItems()->exists() ||
            $product->saleItems()->exists()
        ) {
            return redirect()
                ->route('products.index')
                ->with(
                    'error',
                    'Produk tidak dapat dihapus karena sudah digunakan dalam transaksi.'
                );
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
