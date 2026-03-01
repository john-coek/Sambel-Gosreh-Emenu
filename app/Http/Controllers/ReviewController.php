<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        Review::create([
            'product_id' => $productId,
            'name'  =>  $request->name,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Terima kasih atas penilaiannya!');
    }

   public function index($id) // atau nama method Anda
{
    // 1. Cari produk beserta ulasannya
    $product = Product::with('reviews')->findOrFail($id);

    // 2. Ambil data store (biasanya melalui relasi di model Product)
    // Pastikan di model Product sudah ada public function store()
    $store = $product->store; 

    // 3. Kirim kedua variabel ke view
    return view('pages.review', compact('product', 'store'));
}
}
