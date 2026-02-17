<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_id')
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable(),
                TextColumn::make('size')
                    ->sortable(),
                TextColumn::make('quantity')
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Price')
                    ->formatStateUsing(fn ($state) => '$' . number_format((float) $state, 2)),
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->getStateUsing(fn ($record) => $record->quantity * $record->price)
                    ->formatStateUsing(fn ($state) => '$' . number_format((float) $state, 2)),
            ])
            ->filters([
                //
            ])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
