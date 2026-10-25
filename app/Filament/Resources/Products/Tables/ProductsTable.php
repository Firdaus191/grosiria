<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->searchable(),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                ImageColumn::make('image_path'),
                TextColumn::make('base_price')
                    ->label('Harga Normal')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('discount_price')
                    ->label('Harga Promo (JSM)')
                    ->money('IDR', locale: 'id')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric(locale: 'id')
                    ->badge()
                    ->color(fn ($state): string => (int) $state < 20 ? 'danger' : 'success')
                    ->sortable(),
                TextColumn::make('unit')
                    ->searchable(),
                TextColumn::make('bonus_points')
                    ->label('Bonus Poin')
                    ->numeric(locale: 'id')
                    ->suffix(' G-Poin')
                    ->sortable(),
                TextColumn::make('sold_count')
                    ->numeric()
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
