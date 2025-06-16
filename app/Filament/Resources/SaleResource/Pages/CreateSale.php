<?php


namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\ProductVariant;
use App\Models\SaleItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;
    }

    protected function afterCreate(): void
    {
        $sale = $this->record;
        $items = $this->data['items'] ?? [];
        $total = 0;

        foreach ($items as $item) {
            $variant = ProductVariant::where('product_id', $item['product_id'])
                ->where('color_id', $item['color_id'])
                ->where('size_id', $item['size_id'])
                ->first();

            if (! $variant) {
                throw ValidationException::withMessages([
                    'items' => 'One of the selected product variants does not exist.',
                ]);
            }

            if ($variant->quantity < $item['quantity']) {
                Notification::make()
                    ->title('Insufficient Stock')
                    ->danger()
                    ->body("Only {$variant->quantity} left in stock for {$variant->display_name}.")
                    ->send();

                throw ValidationException::withMessages([
                    'items' => "Insufficient stock for {$variant->display_name}. Only {$variant->quantity} left.",
                ]);
            }

            // Decrement stock
            $variant->decrement('quantity', $item['quantity']);

            // Create sale item
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_variant_id' => $variant->id,
                'quantity' => $item['quantity'],
                'price' => $variant->price,
            ]);

            $total += $variant->price * $item['quantity'];
        }

        $sale->update(['total' => $total]);
    }
}