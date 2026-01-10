<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // ================================================
        // 1. BASE QUERY
        // Mulai dengan produk aktif dan ada stok
        // ================================================
        $query = Product::query()
            ->with(['category', 'primaryImage']) // Eager load relasi
            ->active()
            ->inStock();

        // ================================================
        // 2. FILTER: PENCARIAN
        // Cari di nama dan deskripsi produk
        // ================================================
        if ($request->filled('q')) {
            $query->search($request->q); // Scope search di Model
        }

        // ================================================
        // 3. FILTER: KATEGORI
        // Gunakan slug kategori, bukan ID
        // ================================================
        if ($request->filled('category')) {
            $query->byCategory($request->category); // Scope di Model
        }

        // ================================================
        // 4. FILTER: RENTANG HARGA
        // ================================================
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // ================================================
        // 5. FILTER: DISKON
        // Hanya produk yang sedang diskon
        // ================================================
        if ($request->boolean('on_sale')) {
            $query->onSale(); // Scope: discount_price < price
        }

        // ================================================
        // 6. SORTING
        // Default: terbaru (newest)
        // ================================================
        $sort = $request->get('sort', 'newest');

        match ($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc'   => $query->orderBy('name', 'asc'),
            'name_desc'  => $query->orderBy('name', 'desc'),
            default      => $query->latest(), // newest
        };

        // ================================================
        // 7. PAGINATION
        // withQueryString() menjaga parameter filter di URL pagination
        // ================================================
        $products = $query->paginate(12)->withQueryString();

        // ================================================
        // 8. DATA SIDEBAR
        // Kategori untuk filter
        // ================================================
        $categories = Category::query()
            ->active()
            ->withCount(['activeProducts'])
            ->having('active_products_count', '>', 0)
            ->orderBy('name')
            ->get();

        return view('catalog.index', compact('products', 'categories'));
    }
}
