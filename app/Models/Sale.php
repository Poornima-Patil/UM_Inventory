<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SaleItem;
use App\Models\User;

class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_phone',
        'payment_method',
        'total',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
