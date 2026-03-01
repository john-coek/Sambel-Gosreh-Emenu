@extends('layouts.app')

@section('content')
  <div>
    <div class="p-6 bg-white shadow rounded-lg">
      <h3 class="text-xl font-bold mb-4">Ulasan Produk</h3>

      @if (session()->has('message'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
          {{ session('message') }}
        </div>
      @endif

      <div class="mb-4">
        <label class="block font-medium mb-1">Berikan Bintang:</label>
        <div class="flex gap-1">
          @for ($i = 1; $i <= 5; $i++)
            <button wire:click="$set('rating', {{ $i }})" type="button">
              <svg class="w-8 h-8 {{ $rating >= $i ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor"
                viewBox="0 0 20 20">
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
            </button>
          @endfor
        </div>
      </div>

      <textarea wire:model="comment" class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-blue-500" rows="3"
        placeholder="Apa pendapat Anda tentang produk ini?"></textarea>
      @error('comment')
        <span class="text-red-500 text-sm">{{ $message }}</span>
      @enderror

      <button wire:click="storeReview"
        class="mt-3 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
        Kirim Penilaian
      </button>

      <hr class="my-6">

      <div class="space-y-4">
        <p class="font-semibold text-lg">Semua Ulasan ({{ number_format($average, 1) }} ⭐)</p>
        @foreach ($reviews as $review)
          <div class="border-b pb-4">
            <div class="flex items-center justify-between">
              <span class="font-bold">{{ $review->user->name }}</span>
              <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
            </div>
            <div class="text-yellow-400 text-sm mb-1">
              {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
            </div>
            <p class="text-gray-700">{{ $review->comment }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </div>
@endsection
