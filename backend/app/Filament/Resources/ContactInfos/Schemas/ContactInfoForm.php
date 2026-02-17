<?php

namespace App\Filament\Resources\ContactInfos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ContactInfoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->maxLength(255),
                Textarea::make('address')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('hours')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }
}
