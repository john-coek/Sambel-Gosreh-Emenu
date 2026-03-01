<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Trend Pendapatan SaaS';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $userId = Auth::user()->id;
        $isAdmin = Auth::user()->role === 'admin';

        // Get last 7 days data
        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');

            if ($isAdmin) {
                // For admin: focus on SaaS revenue from subscriptions
                $dailySubscriptions = \App\Models\SubscriptionPayment::where('status', 'success')
                    ->whereDate('created_at', $date->toDateString())
                    ->count();
                $dailyRevenue = $dailySubscriptions * 50000; // Rp 50,000 per subscription
            } else {
                // For store: focus on transaction revenue
                $query = Transaction::where('status', 'success')
                    ->where('user_id', $userId)
                    ->whereDate('created_at', $date->toDateString());
                $dailyRevenue = $query->sum('total_price');
            }

            $data[] = $dailyRevenue;
        }

        return [
            'datasets' => [
                [
                    'label' => $isAdmin ? 'Pendapatan SaaS Harian' : 'Pendapatan Harian',
                    'data' => $data,
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "Rp " + value.toLocaleString("id-ID"); }',
                    ],
                ],
            ],
        ];
    }
}
