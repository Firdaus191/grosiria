<?php

namespace App\Filament\Resources\Vouchers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VouchersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('banner_path')
                    ->searchable(),
                TextColumn::make('discount_type')
                    ->badge(),
                TextColumn::make('discount_value')
                    ->label('Nilai Diskon')
                    ->formatStateUsing(function ($state, $record): string {
                        if ($record->discount_type === 'percentage') {
                            return rtrim(rtrim(number_format((float) $state, 2, ',', '.'), '0'), ',') . '%';
                        }

                        return 'Rp ' . number_format((float) $state, 0, ',', '.');
                    })
                    ->sortable(),
                TextColumn::make('max_discount_amount')
                    ->label('Maks. Diskon')
                    ->money('IDR', locale: 'id')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('min_purchase_amount')
                    ->label('Min. Belanja')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('points_cost')
                    ->label('Biaya Tebus Poin')
                    ->numeric(locale: 'id')
                    ->suffix(' G-Poin')
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('quota')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('used_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
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
