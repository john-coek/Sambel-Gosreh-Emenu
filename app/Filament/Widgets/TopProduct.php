<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\TransactionDetail;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TopProducts extends BaseWidget
{
    protected static ?string $heading = 'Toko Teraktif';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $userId = Auth::user()->id;
        $isAdmin = Auth::user()->role === 'admin';

        if ($isAdmin) {
            // For admin: show top stores by activity
            $query = \App\Models\User::query()
                ->where('role', 'store')
                ->withCount(['transactions as total_transactions' => function ($query) {
                    $query->where('status', 'success');
                }])
                ->withCount(['products as total_products'])
                ->withSum(['transactions as total_revenue' => function ($query) {
                    $query->where('status', 'success');
                }], 'total_price')
                ->withCount(['subscriptions as active_subscriptions' => function ($query) {
                    $query->where('is_active', true);
                }]);

            return $table
                ->query($query->orderBy('total_transactions', 'desc'))
                ->columns([
                    Tables\Columns\ImageColumn::make('logo')
                        ->label('Logo')
                        ->circular()
                        ->size(50),

                    Tables\Columns\TextColumn::make('name')
                        ->label('Nama Toko')
                        ->searchable()
                        ->sortable()
                        ->weight('bold'),

                    Tables\Columns\TextColumn::make('email')
                        ->label('Email')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('total_transactions')
                        ->label('Total Transaksi')
                        ->sortable()
                        ->badge()
                        ->color('success')
                        ->formatStateUsing(fn(int $state): string => $state . ' transaksi'),

                    Tables\Columns\TextColumn::make('total_revenue')
                        ->label('Total Revenue')
                        ->money('IDR')
                        ->sortable(),

                    Tables\Columns\TextColumn::make('total_products')
                        ->label('Total Produk')
                        ->sortable()
                        ->badge()
                        ->color('info')
                        ->formatStateUsing(fn(int $state): string => $state . ' produk'),

                    Tables\Columns\TextColumn::make('active_subscriptions')
                        ->label('Langganan Aktif')
                        ->sortable()
                        ->badge()
                        ->color('warning')
                        ->formatStateUsing(fn(int $state): string => $state ? 'Aktif' : 'Tidak Aktif'),

                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Bergabung')
                        ->date('d M Y')
                        ->sortable(),
                ])
                ->defaultSort('total_transactions', 'desc')
                ->paginated(false);
        } else {
            // For store: show top products
            $query = Product::query()
                ->where('user_id', $userId)
                ->with(['productCategory'])
                ->withCount(['transactionDetails as total_sold' => function ($query) {
                    $query->whereHas('transaction', function ($q) {
                        $q->where('status', 'success');
                    });
                }])
                ->withSum(['transactionDetails as total_revenue' => function ($query) {
                    $query->whereHas('transaction', function ($q) {
                        $q->where('status', 'success');
                    });
                }], DB::raw('quantity * (SELECT price FROM products WHERE products.id = transaction_details.product_id)'));

            return $table
                ->query($query->orderBy('total_sold', 'desc'))
                ->columns([
                    Tables\Columns\ImageColumn::make('image')
                        ->label('Gambar')
                        ->circular()
                        ->size(50),

                    Tables\Columns\TextColumn::make('name')
                        ->label('Nama Produk')
                        ->searchable()
                        ->sortable()
                        ->weight('bold')
                        ->description(fn(Product $record): string => $record->productCategory->name ?? ''),

                    Tables\Columns\TextColumn::make('price')
                        ->label('Harga')
                        ->money('IDR')
                        ->sortable(),

                    Tables\Columns\TextColumn::make('total_sold')
                        ->label('Terjual')
                        ->sortable()
                        ->badge()
                        ->color('success')
                        ->formatStateUsing(fn(int $state): string => $state . ' pcs'),

                    Tables\Columns\TextColumn::make('total_revenue')
                        ->label('Total Revenue')
                        ->money('IDR')
                        ->sortable()
                        ->formatStateUsing(fn($state): string => 'Rp ' . number_format($state ?? 0)),

                    Tables\Columns\IconColumn::make('is_popular')
                        ->label('Populer')
                        ->boolean()
                        ->trueIcon('heroicon-o-star')
                        ->falseIcon('heroicon-o-star')
                        ->trueColor('warning')
                        ->falseColor('gray'),

                    Tables\Columns\TextColumn::make('rating')
                        ->label('Rating')
                        ->sortable()
                        ->formatStateUsing(fn($state): string => $state ? number_format($state, 1) . ' ⭐' : 'Belum ada rating'),
                ])
                ->defaultSort('total_sold', 'desc')
                ->paginated(false);
        }
    }
}
