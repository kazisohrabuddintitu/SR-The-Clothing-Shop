<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Placeholder::make('customer')
                    ->label('Customer')
                    ->content(fn ($record) => $record?->user?->email ?? '-'),
                Placeholder::make('total')
                    ->label('Total')
                    ->content(fn ($record) => $record ? '$' . number_format((float) $record->total, 2) : '-'),
                Select::make('status')
                    ->options([
                        'pending' => 'pending',
                        'processing' => 'processing',
                        'shipped' => 'shipped',
                        'delivered' => 'delivered',
                        'cancelled' => 'cancelled',
                    ])
                    ->required(),
                TextInput::make('address_line1')
                    ->label('Address Line 1')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('address_line2')
                    ->label('Address Line 2')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('city')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('state')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('postal_code')
                    ->label('Postal Code')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('country')
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }
}
