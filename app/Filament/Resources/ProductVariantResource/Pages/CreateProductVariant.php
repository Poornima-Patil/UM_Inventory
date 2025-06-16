<?php

namespace App\Filament\Resources\ProductVariantResource\Pages;

use App\Filament\Resources\ProductVariantResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProductVariant extends CreateRecord
{
    protected static string $resource = ProductVariantResource::class;

     protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['initial_quantity'] = $data['quantity'];
        return $data;
    }
    
}
