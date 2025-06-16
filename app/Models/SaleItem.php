<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Sale;    

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id', 'product_variant_id', 'quantity', 'price'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
    
    protected static function booted()
    {
        static::created(function ($saleItem) {
            // After creating, update sale total
            static::updateSaleTotal($saleItem->sale);
        });

        static::updated(function ($saleItem) {
            // After updating, update sale total
            static::updateSaleTotal($saleItem->sale);
        });

        static::deleted(function ($saleItem) {
            // After deleting, update sale total
            static::updateSaleTotal($saleItem->sale);
        });

        // Keep your stock logic in updating/deleting if needed
        static::updating(function ($saleItem) {
            // Find the original quantity before update
            $originalQuantity = $saleItem->getOriginal('quantity');
            $newQuantity = $saleItem->quantity;

            if ($saleItem->product_variant_id == $saleItem->getOriginal('product_variant_id')) {
                // Same variant, adjust by the difference
                $difference = $originalQuantity - $newQuantity;
                if ($difference !== 0) {
                    $saleItem->productVariant->increment('quantity', $difference);
                }
            } else {
                // Different variant: restore to old, subtract from new
                $oldVariant = \App\Models\ProductVariant::find($saleItem->getOriginal('product_variant_id'));
                if ($oldVariant) {
                    $oldVariant->increment('quantity', $originalQuantity);
                }
                $saleItem->productVariant->decrement('quantity', $newQuantity);
            }
        });

        static::deleting(function ($saleItem) {
            if ($saleItem->productVariant && $saleItem->quantity) {
                $saleItem->productVariant->increment('quantity', $saleItem->quantity);
            }
        });
    }

    protected static function updateSaleTotal($sale)
    {
        if ($sale) {
            // Reload items from the database to avoid stale cache
            $sale->load('items');
            $total = $sale->items->sum(function ($item) {
                return $item->quantity * $item->price;
            });
            $sale->total = $total;
            $sale->save();
        }
    }
}

