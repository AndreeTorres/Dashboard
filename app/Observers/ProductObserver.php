<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    public function creating(Product $product)
    {
        // Automatically set the image path if not provided
        if (empty($product->image)) {
            $defaultImage = base_path('database/seeders/assets/images/default.png');
            $uniqueName = uniqid().'.png';
            $destinationDir = public_path('storage/products');
            if (! file_exists($destinationDir)) {
                mkdir($destinationDir, 0777, true);
            }
            $destinationPath = $destinationDir.'/'.$uniqueName;
            if (file_exists($defaultImage)) {
                copy($defaultImage, $destinationPath);
            }
            $product->image = 'products/'.$uniqueName;
        }
    }
}
