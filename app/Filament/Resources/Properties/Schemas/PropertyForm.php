<?php

namespace App\Filament\Resources\Properties\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PropertyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('lister_id')
                    ->relationship('lister', 'name')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('listing_type')
                    ->required(),
                TextInput::make('property_type')
                    ->required(),
                TextInput::make('status')
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('currency')
                    ->required()
                    ->default('ETB'),
                TextInput::make('area')
                    ->required()
                    ->numeric(),
                TextInput::make('bedrooms')
                    ->numeric(),
                TextInput::make('bathrooms')
                    ->numeric(),
                Toggle::make('is_furnished')
                    ->required(),
                Textarea::make('amenities')
                    ->columnSpanFull(),
                TextInput::make('address_region')
                    ->required(),
                TextInput::make('address_city')
                    ->required(),
                TextInput::make('address_subcity')
                    ->required(),
                TextInput::make('address_specific_area')
                    ->required(),
                TextInput::make('latitude')
                    ->numeric(),
                TextInput::make('longitude')
                    ->numeric(),
                Toggle::make('is_featured')
                    ->required(),
            ]);
    }
}
