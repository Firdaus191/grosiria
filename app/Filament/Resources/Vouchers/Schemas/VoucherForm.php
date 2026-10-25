<?php

namespace App\Filament\Resources\Vouchers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class VoucherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('banner_path'),
                Select::make('discount_type')
                    ->options(['percentage' => 'Percentage', 'fixed' => 'Fixed'])
                    ->live()
                    ->required(),
                TextInput::make('discount_value')
                    ->label('Nilai Diskon')
                    ->required()
                    ->numeric()
                    ->prefix(fn (Get $get): ?string => $get('discount_type') === 'fixed' ? 'Rp' : null)
                    ->suffix(fn (Get $get): ?string => $get('discount_type') === 'percentage' ? '%' : null),
                TextInput::make('max_discount_amount')
                    ->label('Maks. Diskon')
                    ->numeric()
                    ->prefix('Rp'),
                TextInput::make('min_purchase_amount')
                    ->label('Min. Belanja')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('Rp'),
                TextInput::make('points_cost')
                    ->label('Biaya Tebus Poin')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->suffix('G-Poin'),
                TextInput::make('quota')
                    ->required()
                    ->numeric(),
                TextInput::make('used_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('start_date')
                    ->required(),
                DateTimePicker::make('end_date')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
