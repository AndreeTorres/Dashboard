<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderDetail;

class OrderDetailObserver
{
    public function created(OrderDetail $orderDetail)
    {
        $this->updateOrder($orderDetail->order);
    }

    public function updated(OrderDetail $orderDetail)
    {
        $this->updateOrder($orderDetail->order);
    }

    public function deleted(OrderDetail $orderDetail)
    {
        $this->updateOrder($orderDetail->order);
    }

    public function restored(OrderDetail $orderDetail)
    {
        $this->updateOrder($orderDetail->order);
    }

    private function updateOrder(Order $order): void
    {
        $subtotal = $order->orderDetails->sum('subtotal');
        $orderDiscount = $order->discount_percentage > 0 ? $subtotal * ($order->discount_percentage / 100) : 0;
        $tax = $order->tax_included ? 0 : ($subtotal - $orderDiscount) * config('app.vat_rate');
        $total = $subtotal - $orderDiscount + $tax;

        $order->subtotal = $subtotal;
        $order->tax = $tax;
        $order->discount = $orderDiscount;
        $order->total = $total;
        $order->tipping = $order->tipping_percentage > 0 ? $total * ($order->tipping_percentage / 100) : 0;
        $order->save();
    }
}
