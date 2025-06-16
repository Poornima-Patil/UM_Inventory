<?php

namespace App\Filament\Resources\ProductResource\Pages\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class ProductVariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'productVariants'; // relation name on Product model

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('color_id')
                    ->relationship('color', 'name')
                    ->required(),

                Forms\Components\Select::make('size_id')
                    ->relationship('size', 'name')
                    ->required(),

                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('color.name')->label('Color'),
                Tables\Columns\TextColumn::make('size.name')->label('Size'),
                Tables\Columns\TextColumn::make('price')->money('INR'),
                Tables\Columns\TextColumn::make('quantity'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }
}
