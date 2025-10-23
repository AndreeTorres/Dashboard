<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplyCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
    ];

    public function supplies()
    {
        return $this->hasMany(Supply::class);
    }
}
