<?php

namespace App\Filament\Resources;

use App\Models\Size;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class SizeResource extends Resource
{
    protected static ?string $model = Size::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrows-pointing-out';

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
            'index' => \App\Filament\Resources\SizeResource\Pages\ListSizes::route('/'),
            'create' => \App\Filament\Resources\SizeResource\Pages\CreateSize::route('/create'),
            'edit' => \App\Filament\Resources\SizeResource\Pages\EditSize::route('/{record}/edit'),
        ];
    }
}
