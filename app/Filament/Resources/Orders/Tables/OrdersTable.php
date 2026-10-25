<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('user_address_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('voucher.title')
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
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('subtotal_amount')
                    ->label('Subtotal')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('shipping_cost')
                    ->label('Ongkir')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('voucher_discount_amount')
                    ->label('Diskon Voucher')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('points_redeemed')
                    ->numeric(locale: 'id')
                    ->suffix(' G-Poin')
                    ->sortable(),
                TextColumn::make('points_discount_amount')
                    ->label('Diskon Poin')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('grand_total')
                    ->label('Grand Total')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('points_earned')
                    ->numeric(locale: 'id')
                    ->suffix(' G-Poin')
                    ->sortable(),
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
                TextColumn::make('receipt_pdf_path')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
