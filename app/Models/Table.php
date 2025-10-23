<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'capacity',
        'available',
    ];

    protected $casts = [
        'available' => 'boolean',
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_table');
    }

    public function scopeAvailable($query)
    {
        return $query->where('available', true);
    }
}
