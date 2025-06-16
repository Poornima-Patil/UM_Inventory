<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Models\Sale;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use App\Filament\Resources\SaleResource\RelationManagers\ItemsRelationManager;
class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-rupee';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Section::make('Products')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->label('Products')
                        ->minItems(1)
                        ->schema([
                            Forms\Components\Grid::make(5)
                                ->schema([
                                    Forms\Components\Select::make('product_id')
                                        ->label('Product')
                                        ->options(\App\Models\Product::all()->pluck('name', 'id'))
                                        ->reactive(),
                                    Forms\Components\Select::make('color_id')
                                        ->label('Color')
                                        ->options(fn (callable $get) => $get('product_id')
                                            ? \App\Models\ProductVariant::where('product_id', $get('product_id'))
                                                ->with('color')
                                                ->get()
                                                ->pluck('color.name', 'color_id')
                                                ->unique()
                                            : [])
                                        ->reactive(),
                                    Forms\Components\Select::make('size_id')
                                        ->label('Size')
                                        ->options(fn (callable $get) => $get('product_id') && $get('color_id')
                                            ? \App\Models\ProductVariant::where('product_id', $get('product_id'))
                                                ->where('color_id', $get('color_id'))
                                                ->with('size')
                                                ->get()
                                                ->pluck('size.name', 'size_id')
                                                ->unique()
                                            : [])
                                        ->reactive(),
                                    Forms\Components\TextInput::make('quantity')
                                        ->numeric()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set, callable $get, $livewire) {
                                            $items = $get('../../items') ?? [];
                                            $total = 0;
                                            foreach ($items as $item) {
                                                if (
                                                    isset($item['product_id'], $item['color_id'], $item['size_id'], $item['quantity'])
                                                    && $item['product_id'] && $item['color_id'] && $item['size_id'] && $item['quantity']
                                                ) {
                                                    $variant = \App\Models\ProductVariant::where('product_id', $item['product_id'])
                                                        ->where('color_id', $item['color_id'])
                                                        ->where('size_id', $item['size_id'])
                                                        ->first();
                                                    if ($variant) {
                                                        $total += $variant->price * (int) $item['quantity'];
                                                    }
                                                }
                                            }
                                            $set('total', $total);
                                        }),
                                    Forms\Components\Placeholder::make('stock_left')
                                        ->label('Stock Left')
                                        ->content(function (callable $get) {
                                            $productId = $get('product_id');
                                            $colorId = $get('color_id');
                                            $sizeId = $get('size_id');
                                            if ($productId && $colorId && $sizeId) {
                                                $variant = \App\Models\ProductVariant::where('product_id', $productId)
                                                    ->where('color_id', $colorId)
                                                    ->where('size_id', $sizeId)
                                                    ->first();
                                                if ($variant) {
                                                    return $variant->quantity . ' in stock';
                                                }
                                            }
                                            return 'Select product, color, and size';
                                        })
                                        ->reactive(),
                                ]),
                        ])
                        ->createItemButtonLabel('Add Product')
                        ->reactive(),
                    Forms\Components\Placeholder::make('total')
                        ->label('Total Amount')
                        ->content(function (callable $get) {
                            $items = $get('items') ?? [];
                            $total = 0;
                            foreach ($items as $item) {
                                if (
                                    isset($item['product_id'], $item['color_id'], $item['size_id'], $item['quantity'])
                                    && $item['product_id'] && $item['color_id'] && $item['size_id'] && $item['quantity']
                                ) {
                                    $variant = \App\Models\ProductVariant::where('product_id', $item['product_id'])
                                        ->where('color_id', $item['color_id'])
                                        ->where('size_id', $item['size_id'])
                                        ->first();
                                    if ($variant) {
                                        $total += $variant->price * (int) $item['quantity'];
                                    }
                                }
                            }
                            return '₹ ' . number_format($total, 2);
                        })
                        ->reactive(),
                ])->columns(1),

            Forms\Components\Section::make('Customer & Payment Details')
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('customer_name')->required(),
                            Forms\Components\TextInput::make('customer_phone')->required(),
                            Forms\Components\Select::make('payment_method')
                                ->options(['UPI' => 'UPI', 'Cash' => 'Cash'])->required(),
                        ]),
                ])->columns(1),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('customer_name')->label('Customer'),
            Tables\Columns\TextColumn::make('customer_phone')->label('Phone'),
            Tables\Columns\TextColumn::make('payment_method')->label('Payment'),
            Tables\Columns\TextColumn::make('total')->money('INR')->sortable(),
        ]) ->actions([
        Tables\Actions\ViewAction::make(),
        Tables\Actions\EditAction::make(),
    ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
            'view' => Pages\ViewSale::route('/{record}'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Sale Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('customer_name')->label('Customer Name'),
                        Infolists\Components\TextEntry::make('customer_phone')->label('Customer Phone'),
                        Infolists\Components\TextEntry::make('payment_method')->label('Payment Method'),
                        Infolists\Components\TextEntry::make('total')->label('Total Amount')->money('INR'),
                    ]),
                Infolists\Components\Section::make('Sale Items')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->schema([
                                Infolists\Components\TextEntry::make('productVariant.display_name')->label('Product Variant'),
                                Infolists\Components\TextEntry::make('quantity'),
                                Infolists\Components\TextEntry::make('price')->money('INR'),
                                Infolists\Components\TextEntry::make('total_price')
                                    ->label('Total Price')
                                    ->state(function ($record) {
                                        // $record is the SaleItem
                                        return $record->quantity && $record->price
                                            ? '₹ ' . number_format($record->quantity * $record->price, 2)
                                            : '-';
                                    }),
                            ])
                            ->columns(4), // Now 4 columns: Variant, Quantity, Price, Total Price
                    ]),
            ]);
    }
    public static function getRelations(): array
{
    return [
        // Define any relations here if neede
        ItemsRelationManager::class,
    ];
}
protected static function updateSaleTotal($sale)
{
    if ($sale) {
        // Always get the latest from the DB, not from the relationship
        $total = \App\Models\SaleItem::where('sale_id', $sale->id)
            ->sum(\DB::raw('quantity * price'));
        $sale->total = $total;
        $sale->save();
    }
}
}