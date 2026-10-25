<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('order_number')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('user_address_id')
                    ->numeric(),
                Select::make('voucher_id')
                    ->relationship('voucher', 'title'),
                Select::make('fulfillment_method')
                    ->options(['delivery' => 'Delivery', 'pickup' => 'Pickup'])
                    ->default('delivery')
                    ->required(),
                TextInput::make('pickup_code'),
                TextInput::make('subtotal_amount')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                TextInput::make('shipping_cost')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('Rp'),
                TextInput::make('voucher_discount_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('Rp'),
                TextInput::make('points_redeemed')
                    ->required()
                    ->numeric()
                    ->suffix('G-Poin')
                    ->default(0),
                TextInput::make('points_discount_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('Rp'),
                TextInput::make('grand_total')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                TextInput::make('points_earned')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('order_status')
                    ->options([
            'pending_payment' => 'Pending payment',
            'processing' => 'Processing',
            'ready_for_pickup' => 'Ready for pickup',
            'on_delivery' => 'On delivery',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ])
                    ->default('pending_payment')
                    ->required(),
                TextInput::make('receipt_pdf_path'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
