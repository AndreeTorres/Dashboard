<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supply extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'supply_category_id',
    ];

    public function supplyCategory()
    {
        return $this->belongsTo(SupplyCategory::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
