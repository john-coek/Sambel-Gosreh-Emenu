<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionAnalytics extends ChartWidget
{
    protected static ?string $heading = 'Analisis Langganan';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $userId = Auth::user()->id;
        $isAdmin = Auth::user()->role === 'admin';

        if ($isAdmin) {
            // For admin: focus on subscription analytics
            $query = \App\Models\SubscriptionPayment::query();

            $statusData = $query->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $labels = ['Berhasil', 'Pending', 'Gagal'];
            $data = [
                $statusData['success'] ?? 0,
                $statusData['pending'] ?? 0,
                $statusData['failed'] ?? 0,
            ];

            return [
                'datasets' => [
                    [
                        'label' => 'Jumlah Langganan',
                        'data' => $data,
                        'backgroundColor' => [
                            'rgb(34, 197, 94)',   // Success - Green
                            'rgb(251, 191, 36)',  // Pending - Yellow
                            'rgb(239, 68, 68)',   // Failed - Red
                        ],
                        'borderWidth' => 0,
                    ],
                ],
                'labels' => $labels,
            ];
        } else {
            // For store: focus on transaction analytics
            $query = Transaction::query()->where('user_id', $userId);

            $statusData = $query->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $labels = ['Berhasil', 'Pending', 'Gagal'];
            $data = [
                $statusData['success'] ?? 0,
                $statusData['pending'] ?? 0,
                $statusData['failed'] ?? 0,
            ];

            return [
                'datasets' => [
                    [
                        'label' => 'Jumlah Transaksi',
                        'data' => $data,
                        'backgroundColor' => [
                            'rgb(34, 197, 94)',   // Success - Green
                            'rgb(251, 191, 36)',  // Pending - Yellow
                            'rgb(239, 68, 68)',   // Failed - Red
                        ],
                        'borderWidth' => 0,
                    ],
                ],
                'labels' => $labels,
            ];
        }
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}
