<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'color_id', 'size_id', 'price', 'quantity','initial_quantity'];

    public function product() { return $this->belongsTo(Product::class); }
    public function color() { return $this->belongsTo(Color::class); }
    public function size() { return $this->belongsTo(Size::class); }
    public function sales() { return $this->hasMany(Sale::class); }
public function saleItems()
{
    return $this->hasMany(\App\Models\SaleItem::class, 'product_variant_id');
}

    public function getDisplayNameAttribute()
{
    return "{$this->product->name} - {$this->color->name} - {$this->size->name}";
}



}
