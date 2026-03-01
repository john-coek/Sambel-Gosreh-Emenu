<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\User;
use Livewire\Component;

class StoreSearch extends Component
{
    public $search = '';
    public $store;

    public function mount(User $store)
    {
        $this->store = $store;
    }
    public function render()
    {
        // Filter produk berdasarkan input search
        $products = Product::where('store_id', $this->store->id)
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->get();

        // Ambil produk populer (bisa tetap atau ikut terfilter)
        $populars = Product::where('store_id', $this->store->id)
            ->where('is_popular', true)
            ->take(5)
            ->get();
        return view('livewire.store-search', [
            'products' => $products,
            'populars' => $populars,
            'categories' => $this->store->productCategories
        ]);
    }
}
