<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTable extends Model
{
    protected $fillable = [
        'order_id',
        'table_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
