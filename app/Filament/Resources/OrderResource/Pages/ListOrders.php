<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('instant_create')
                ->label(
                    __('filament-actions::create.single.label', ['label' => __('Order')])
                )
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->action(function () {
                    $order = Order::create([
                        'user_id' => Filament::auth()->id(),
                    ]);

                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $order->id]));
                })
                ->requiresConfirmation(false),
        ];
    }
}
