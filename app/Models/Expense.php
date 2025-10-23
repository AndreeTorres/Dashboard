<?php

namespace App\Models;

use App\Enums\MeasureUnitEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supply_id',
        'cost',
        'quantity',
        'measure_unit',
        'notes',
        'date',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'quantity' => 'decimal:2',
        'measure_unit' => MeasureUnitEnum::class,
        'date' => 'date',
    ];

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }
}
