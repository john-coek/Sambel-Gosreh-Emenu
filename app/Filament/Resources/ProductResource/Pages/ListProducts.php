<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ProductResource;
use App\Models\Product;
use App\Models\Subscription;

use function Symfony\Component\Clock\now;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        if (Auth::user()->role === 'admin') {
            return [
                Actions\CreateAction::make(),
            ];
        }

        $subscription = Subscription::where('user_id', Auth::user()->id)
            ->where('end_date', '>', now())
            ->where('is_active', true)
            ->latest()
            ->first();
        $countProduct = Product::where('user_id', Auth::user()->id)->count();

        return [
            Actions\Action::make('alert')
                ->label('Anda telah mencapai batas penggunaan, silahkan berlangganan untuk melanjutkan kembali')
                ->color('danger')
                ->icon('heroicon-s-exclamation-triangle')
                ->visible(!$subscription && $countProduct >=2),
            Actions\CreateAction::make(),
        ];
    }
}
