<div class="review-form">
    <h4>Tinggalkan Ulasan</h4>
    <form action="{{ route('review.store', $product->id) }}" method="POST">
        @csrf
        
        <div class="mb-3">
            <label>Nama Anda:</label>
            <input type="text" name="name" class="form-control" placeholder="Masukkan nama..." required>
        </div>

        <div class="mb-3">
            <label>Rating Produk:</label>
            <select name="rating" class="form-control" required>
                <option value="5">⭐⭐⭐⭐⭐ (Sangat Puas)</option>
                <option value="4">⭐⭐⭐⭐ (Puas)</option>
                <option value="3">⭐⭐⭐ (Cukup)</option>
                <option value="2">⭐⭐ (Buruk)</option>
                <option value="1">⭐ (Sangat Buruk)</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Komentar:</label>
            <textarea name="comment" rows="4" class="form-control" placeholder="Tulis ulasan Anda di sini..." required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
    </form>
</div>

<hr>

<div class="review-list">
    @foreach($product->reviews->sortByDesc('created_at') as $review)
        <div class="review-item" style="margin-bottom: 20px; border-bottom: 1px solid #ddd;">
            <strong>{{ $review->name }}</strong> 
            <span style="color: #f39c12;">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
            <p>{{ $review->comment }}</p>
            <small class="text-muted">{{ $review->created_at->format('d M Y') }}</small>
        </div>
    @endforeach
</div>