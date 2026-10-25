<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestOmnichannelOrders extends TableWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Pesanan Omnichannel Terbaru')
            ->query(
                Order::query()
                    ->with('user:id,name') // eager load: bebas N+1
                    ->latest('id')
            )
            ->defaultPaginationPageOption(10)
            ->poll('10s')
            ->columns([
                TextColumn::make('order_number')
                    ->label('No. Order')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('user.name')
                    ->label('Pelanggan')
                    ->searchable(),
                TextColumn::make('fulfillment_method')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pickup' => 'Pickup',
                        'delivery' => 'Delivery',
                        default => (string) $state,
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'pickup' => 'info',
                        'delivery' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('pickup_code')
                    ->label('PIN Pickup')
                    ->badge()
                    ->color('info')
                    ->copyable()
                    ->placeholder('-'),
                TextColumn::make('grand_total')
                    ->label('Grand Total')
                    ->money('IDR', locale: 'id'),
                TextColumn::make('order_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => str($state)->replace('_', ' ')->headline()->toString())
                    ->color(fn (?string $state): string => match ($state) {
                        'pending_payment' => 'gray',
                        'processing' => 'warning',
                        'ready_for_pickup' => 'info',
                        'on_delivery' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since(),
            ])
            ->paginated([5, 10, 25]);
    }
}
