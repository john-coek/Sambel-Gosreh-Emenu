<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class RecentActivities extends BaseWidget
{
    protected static ?string $heading = 'Aktivitas Terbaru';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $userId = Auth::user()->id;
        $isAdmin = Auth::user()->role === 'admin';

        if ($isAdmin) {
            // For admin: show recent subscription payments
            $query = \App\Models\SubscriptionPayment::with(['subscription.user'])
                ->latest()
                ->limit(10);

            return $table
                ->query($query)
                ->columns([
                    Tables\Columns\TextColumn::make('subscription.user.name')
                        ->label('Nama Toko')
                        ->searchable()
                        ->sortable()
                        ->weight('bold'),

                    Tables\Columns\TextColumn::make('subscription.user.email')
                        ->label('Email Toko')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('amount')
                        ->label('Jumlah')
                        ->money('IDR')
                        ->default('Rp 50.000')
                        ->sortable(),

                    Tables\Columns\BadgeColumn::make('status')
                        ->label('Status')
                        ->colors([
                            'success' => 'success',
                            'warning' => 'pending',
                            'danger' => 'failed',
                        ])
                        ->icons([
                            'heroicon-o-check-circle' => 'success',
                            'heroicon-o-clock' => 'pending',
                            'heroicon-o-x-circle' => 'failed',
                        ]),

                    Tables\Columns\TextColumn::make('subscription.end_date')
                        ->label('Berlaku Sampai')
                        ->date('d M Y')
                        ->sortable(),

                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Waktu Pembayaran')
                        ->dateTime('d M Y H:i')
                        ->sortable()
                        ->since(),
                ])
                ->defaultSort('created_at', 'desc')
                ->paginated(false);
        } else {
            // For store: show recent transactions
            $query = Transaction::with('user')
                ->where('user_id', $userId)
                ->latest()
                ->limit(10);

            return $table
                ->query($query)
                ->columns([
                    Tables\Columns\TextColumn::make('code')
                        ->label('Kode Transaksi')
                        ->searchable()
                        ->sortable()
                        ->copyable()
                        ->weight('bold'),

                    Tables\Columns\TextColumn::make('name')
                        ->label('Nama Pelanggan')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('total_price')
                        ->label('Total')
                        ->money('IDR')
                        ->sortable(),

                    Tables\Columns\BadgeColumn::make('status')
                        ->label('Status')
                        ->colors([
                            'success' => 'success',
                            'warning' => 'pending',
                            'danger' => 'failed',
                        ])
                        ->icons([
                            'heroicon-o-check-circle' => 'success',
                            'heroicon-o-clock' => 'pending',
                            'heroicon-o-x-circle' => 'failed',
                        ]),

                    Tables\Columns\TextColumn::make('payment_method')
                        ->label('Metode Pembayaran')
                        ->badge()
                        ->color('info'),

                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Waktu')
                        ->dateTime('d M Y H:i')
                        ->sortable()
                        ->since(),
                ])
                ->defaultSort('created_at', 'desc')
                ->paginated(false);
        }
    }
}
