<?php

namespace App\Models;

use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(ProductObserver::class)]
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'available',
        'product_subcategory_id',
        'image',
    ];

    protected $casts = [
        'available' => 'boolean',
        'price' => 'float',
    ];

    public function subcategory()
    {
        return $this->belongsTo(ProductSubcategory::class, 'product_subcategory_id');
    }

    public function category()
    {
        return $this->belongsToThrough(ProductCategory::class, ProductSubcategory::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('available', true);
    }
}
