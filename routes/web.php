<?php

use Illuminate\Support\Facades\Route;
use App\Exports\StatisticsExport;
use App\Models\Order;
use App\Models\Expense;
use App\Models\Product;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    return redirect()->to('/admin');
});

Route::get('/health-check', function () {
    return response()->json(['status' => 'ok']);
});

// Rutas de exportación
Route::middleware(['auth'])->group(function () {
    Route::get('/export/orders', function () {
        $orders = Order::with(['user', 'orderDetails.product'])->get();
        $data = [['ID', 'Usuario', 'Estado', 'Tipo', 'Subtotal', 'Impuesto', 'Total', 'Fecha']];
        
        foreach ($orders as $order) {
            $data[] = [
                $order->identifier,
                $order->user->name ?? 'N/A',
                $order->status,
                $order->order_type,
                number_format((float) $order->subtotal, 2),
                number_format((float) $order->tax, 2),
                number_format((float) $order->total, 2),
                $order->created_at->format('d/m/Y H:i')
            ];
        }
        
        return Excel::download(new StatisticsExport($data), 'ordenes-' . now()->format('Y-m-d') . '.xlsx');
    })->name('filament.admin.resources.orders.export');
    
    Route::get('/export/expenses', function () {
        $expenses = Expense::all();
        $data = [['ID', 'Descripción', 'Monto', 'Fecha']];
        
        foreach ($expenses as $expense) {
            $data[] = [
                $expense->id,
                $expense->description ?? 'N/A',
                number_format((float) $expense->amount, 2),
                $expense->created_at->format('d/m/Y H:i')
            ];
        }
        
        return Excel::download(new StatisticsExport($data), 'gastos-' . now()->format('Y-m-d') . '.xlsx');
    })->name('filament.admin.resources.expenses.export');
    
    Route::get('/export/products', function () {
        $products = Product::with(['category', 'subcategory'])->get();
        $data = [['ID', 'Nombre', 'Categoría', 'Subcategoría', 'Precio', 'Fecha Creación']];
        
        foreach ($products as $product) {
            $data[] = [
                $product->id,
                $product->name,
                $product->category->name ?? 'N/A',
                $product->subcategory->name ?? 'N/A', 
                number_format((float) $product->price, 2),
                $product->created_at->format('d/m/Y H:i')
            ];
        }
        
        return Excel::download(new StatisticsExport($data), 'productos-' . now()->format('Y-m-d') . '.xlsx');
    })->name('filament.admin.resources.products.export');
});
