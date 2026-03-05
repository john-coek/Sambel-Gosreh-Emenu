<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function find(Request $request)
    {
        $store = User::where('username', $request->username)->first();

        if(!$store) {
            abort(404);
        }

        return view('pages.find', compact('store'));
    }

   public function findResults(Request $request)
{
    // Gunakan firstOrFail agar otomatis 404 jika username tidak ada
    $store = User::where('username', $request->username)->firstOrFail();

    // Mulai Query
    $query = Product::where('user_id', $store->id);

    // Filter berdasarkan Kategori
    if ($request->filled('category')) {
        $category = ProductCategory::where('user_id', $store->id)
            ->where('slug', $request->category)
            ->first();

        if ($category) {
            $query->where('product_category_id', $category->id);
        }
    }

    // Filter berdasarkan Search
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%'); 
    }

    // Ambil data produk
    $products = $query->get();

    return view('pages.result', compact('store', 'products'));
}

    public function show(Request $request)
    {
        $store = User::where('username', $request->username)->first();

        if (!$store) {
            abort(404);
        }

        $product = Product::where('id', $request->id)->first();

        if (!$store) {
            abort(404);
        }

        return view('pages.product', compact('store', 'product'));

        $store = User::where('username', $request->username)->firstOrFail();

        // Ambil product + relasi yang dibutuhkan
        $product = Product::with([
            'productCategory',
            'productIngredients',
            'reviews' // penting untuk ditampilkan di blade
        ])->where('id', $request->id)->firstOrFail();

        /**
         * ===============================
         * HITUNG RATING DARI REVIEWS
         * ===============================
         */

        // Ambil rata-rata rating langsung dari database (lebih akurat & ringan)
        $averageRating = $product->reviews()->avg('rating') ?? 0;

        // Bulatkan untuk jumlah bintang (maksimal 5)
        $roundedRating = min(round($averageRating), 5);

        // Hitung total review
        $totalReviews = $product->reviews()->count();

        /**
         * (Optional) Statistik rating per bintang
         * berguna kalau nanti mau bikin grafik seperti marketplace
         */
        $ratingBreakdown = [
            5 => $product->reviews()->where('rating', 5)->count(),
            4 => $product->reviews()->where('rating', 4)->count(),
            3 => $product->reviews()->where('rating', 3)->count(),
            2 => $product->reviews()->where('rating', 2)->count(),
            1 => $product->reviews()->where('rating', 1)->count(),
        ];

        return view('pages.review', compact(
            'store',
            'product',
            'averageRating',
            'roundedRating',
            'totalReviews',
            'ratingBreakdown'
        ));
    }

    public function reviewStore(Request $request, $id)
{
    $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string',
        'name' => 'required|string|max:255',
    ]);

    Review::create([
        'product_id' => $id,
        'rating'     => $request->rating,
        'comment'    => $request->comment, // Ubah dari 'content' ke 'comment'
        'name'       => $request->name,    // Tambahkan ini agar nama tersimpan
    ]);

    return redirect()->back()->with('success', 'Ulasan berhasil dikirim!');
}
}
