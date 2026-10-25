<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Select::make('role')
                    ->options(['admin' => 'Admin', 'customer' => 'Customer'])
                    ->default('customer')
                    ->required(),
                TextInput::make('points_balance')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('member_tier')
                    ->options(['reguler' => 'Reguler', 'silver' => 'Silver', 'gold' => 'Gold', 'platinum' => 'Platinum'])
                    ->default('reguler')
                    ->required(),
                DateTimePicker::make('email_verified_at'),
            ]);
    }
}
