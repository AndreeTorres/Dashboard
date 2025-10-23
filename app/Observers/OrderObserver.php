<?php

namespace App\Observers;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\Table;

class OrderObserver
{
    public function creating(Order $order)
    {
        if (empty($order->identifier)) {
            $date = now()->format('Ymd');
            $count = Order::whereDate('created_at', now())->count() + 1;
            $lenght = 4;
            if ($count > 9999) {
                $lenght = strlen($count); // strlen(10000) = 5, so it will pad to 5 digits
            }
            $dailySequence = str_pad($count, $lenght, '0', STR_PAD_LEFT);
            $order->identifier = "{$date}-{$dailySequence}";
        }

        // Set default status if not provided
        if (empty($order->status)) {
            $order->status = OrderStatusEnum::PENDING;
        }
    }

    public function created(Order $order)
    {
        $tables = $order->tables;
        if ($tables->isNotEmpty() && (
            $order->status === OrderStatusEnum::PENDING ||
            $order->status === OrderStatusEnum::IN_PROGRESS
        )) {
            Table::whereIn('id', $tables->pluck('id'))
                ->update(['available' => false]);
        }
    }

    public function updated(Order $order)
    {
        if ($order->isDirty('status') && (
            $order->status === OrderStatusEnum::CANCELLED ||
            $order->status === OrderStatusEnum::COMPLETED
        )) {
            $tables = $order->tables;
            if ($tables->isNotEmpty()) {
                Table::whereIn('id', $tables->pluck('id'))
                    ->update(['available' => true]);
            }
        }

        if ($order->isDirty('tax_included')) {
            $order->tax = $order->tax_included ? 0 : $order->subtotal * config('app.vat_rate');
        }
    }

    public function deleting(Order $order)
    {
        $tables = $order->tables;
        if ($tables->isNotEmpty()) {
            Table::whereIn('id', $tables->pluck('id'))
                ->update(['available' => true]);
        }
    }
}
