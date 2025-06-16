<?php

namespace App\Filament\Resources;

use App\Models\Color;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class ColorResource extends Resource
{
    protected static ?string $model = Color::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
        ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ColorResource\Pages\ListColors::route('/'),
            'create' => \App\Filament\Resources\ColorResource\Pages\CreateColor::route('/create'),
            'edit' => \App\Filament\Resources\ColorResource\Pages\EditColor::route('/{record}/edit'),
        ];
    }
}
