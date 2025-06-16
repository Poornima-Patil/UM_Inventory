<?php

namespace App\Filament\Resources;

use App\Models\ProductVariant;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class ProductVariantResource extends Resource
{
    protected static ?string $model = ProductVariant::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?string $navigationLabel = 'Product Variants';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('product_id')
                ->relationship('product', 'name')->required(),

            Forms\Components\Select::make('color_id')
                ->relationship('color', 'name')->required(),

            Forms\Components\Select::make('size_id')
                ->relationship('size', 'name')->required(),

            Forms\Components\TextInput::make('price')->required()->numeric(),
            Forms\Components\TextInput::make('quantity')->required()->numeric(),
            Forms\Components\Hidden::make('initial_quantity'),

        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('product.name')->label('Product')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('color.name')->sortable(),
            Tables\Columns\TextColumn::make('size.name')->sortable(),
            Tables\Columns\TextColumn::make('price')->money('INR')->sortable(),
            Tables\Columns\TextColumn::make('quantity')->sortable()
            ->label('Available Stock'),
            Tables\Columns\TextColumn::make('initial_quantity')
                ->label('Initial Stock')
                ->sortable(),
              
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ProductVariantResource\Pages\ListProductVariants::route('/'),
            'create' => \App\Filament\Resources\ProductVariantResource\Pages\CreateProductVariant::route('/create'),
            'edit' => \App\Filament\Resources\ProductVariantResource\Pages\EditProductVariant::route('/{record}/edit'),
        ];
    }
}
