<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SubscriptionPayment;
use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DashboardOverview extends BaseWidget
{
    protected function getStats(): array
    {
        if (Auth::user()->role === 'admin') {
            return $this->getAdminStats();
        } else {
            return $this->getStoreStats();
        }
    }

    private function getAdminStats(): array
    {
        $totalUsers = User::count();
        $activeSubscriptions = SubscriptionPayment::where('status', 'success')->count();
        $totalSubscriptionRevenue = $activeSubscriptions * 50000;
        $totalProducts = Product::count();
        $totalTransactions = Transaction::where('status', 'success')->count();

        // Growth calculations
        $lastMonthUsers = User::where('created_at', '>=', now()->subMonth())->count();
        $lastMonthSubscriptions = SubscriptionPayment::where('status', 'success')
            ->where('created_at', '>=', now()->subMonth())->count();
        $lastMonthRevenue = $lastMonthSubscriptions * 50000;

        return [
            Stat::make('Total Pengguna', $totalUsers)
                ->description($lastMonthUsers . ' pengguna baru bulan ini')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->icon('heroicon-o-users'),

            Stat::make('Total Pendapatan SaaS', 'Rp ' . number_format($totalSubscriptionRevenue))
                ->description('Rp ' . number_format($lastMonthRevenue) . ' bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('Total Langganan Aktif', $activeSubscriptions)
                ->description($lastMonthSubscriptions . ' langganan baru bulan ini')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info')
                ->icon('heroicon-o-credit-card'),

            Stat::make('Total Produk Platform', $totalProducts)
                ->description('Produk dari semua toko')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('warning')
                ->icon('heroicon-o-cube'),
        ];
    }

    private function getStoreStats(): array
    {
        $userId = Auth::user()->id;

        $totalTransaction = Transaction::where('user_id', $userId)
            ->where('status', 'success')->count();

        $totalAmount = Transaction::where('user_id', $userId)
            ->where('status', 'success')->sum('total_price');

        $totalProducts = Product::where('user_id', $userId)->count();

        $totalCategories = ProductCategory::where('user_id', $userId)->count();

        // This month stats
        $thisMonthTransactions = Transaction::where('user_id', $userId)
            ->where('status', 'success')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $thisMonthRevenue = Transaction::where('user_id', $userId)
            ->where('status', 'success')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_price');

        // Popular products count
        $popularProducts = Product::where('user_id', $userId)
            ->where('is_popular', true)->count();

        // Average transaction value
        $avgTransactionValue = $totalTransaction > 0 ? $totalAmount / $totalTransaction : 0;

        return [
            Stat::make('Total Transaksi', $totalTransaction)
                ->description($thisMonthTransactions . ' transaksi bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->icon('heroicon-o-receipt-percent'),

            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalAmount))
                ->description('Rp ' . number_format($thisMonthRevenue) . ' bulan ini')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->icon('heroicon-o-banknotes'),

            Stat::make('Total Produk', $totalProducts)
                ->description($popularProducts . ' produk populer')
                ->descriptionIcon('heroicon-m-star')
                ->color('info')
                ->icon('heroicon-o-cube'),

            Stat::make('Rata-Rata Transaksi', 'Rp ' . number_format($avgTransactionValue))
                ->description($totalCategories . ' kategori produk')
                ->descriptionIcon('heroicon-m-tag')
                ->color('warning')
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}
