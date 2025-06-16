<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\Widget;

class SalesByProductWidget extends Widget
{
    protected static string $view = 'filament.widgets.sales-by-product-widget';

    public $sales = [];

    public function mount()
    {
        $products = Product::with(['variants.saleItems.sale'])->get();

        $data = [];

        foreach ($products as $product) {
            $total = 0;
            $cash = 0;
            $upi = 0;

            foreach ($product->variants as $variant) {
                foreach ($variant->saleItems as $saleItem) {
                    $amount = $saleItem->quantity * $saleItem->price;
                    $total += $amount;
                    if ($saleItem->sale && $saleItem->sale->payment_method === 'Cash') {
                        $cash += $amount;
                    } elseif ($saleItem->sale && $saleItem->sale->payment_method === 'UPI') {
                        $upi += $amount;
                    }
                }
            }

            $data[] = [
                'product' => $product->name,
                'total' => $total,
                'cash' => $cash,
                'upi' => $upi,
            ];
        }

        $this->sales = $data;
    }
}
