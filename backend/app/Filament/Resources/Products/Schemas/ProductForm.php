<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('category')
                    ->maxLength(255),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->integer()
                    ->minValue(0),
                FileUpload::make('image_url')
                    ->label('Image')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->visibility('public')
                    ->getStateUsing(fn ($record) => $record?->getRawOriginal('image_url'))
                    ->helperText('Upload a product image.')
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->rows(5)
                    ->columnSpanFull(),
            ]);
    }
}
