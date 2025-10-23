<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total de Órdenes</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ \App\Models\Order::count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Ingresos Totales</dt>
                            <dd class="text-lg font-medium text-gray-900">HNL {{ number_format((float) \App\Models\Order::sum('total'), 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 0h10a2 2 0 002-2v-2a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Gastos Totales</dt>
                            <dd class="text-lg font-medium text-gray-900">HNL {{ number_format((float) \App\Models\Expense::sum('amount'), 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Exportar Estadísticas a Excel</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Exporta diferentes tipos de reportes de estadísticas en formato Excel.
                </p>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="border rounded-lg p-4 text-center">
                        <div class="mb-3">
                            <svg class="h-12 w-12 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h4 class="font-medium text-gray-900">Estadísticas Generales</h4>
                        <p class="text-sm text-gray-500 mt-1">Exporta resumen completo de ventas, gastos y ganancias.</p>
                    </div>
                    <div class="border rounded-lg p-4 text-center">
                        <div class="mb-3">
                            <svg class="h-12 w-12 text-yellow-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                        <h4 class="font-medium text-gray-900">Top Productos</h4>
                        <p class="text-sm text-gray-500 mt-1">Lista de productos más vendidos con cantidades e ingresos.</p>
                    </div>
                    <div class="border rounded-lg p-4 text-center">
                        <div class="mb-3">
                            <svg class="h-12 w-12 text-blue-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-8 0h8m-8 0H6a2 2 0 00-2 2v6a2 2 0 002 2h2m8-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2m-8 4h8"></path>
                            </svg>
                        </div>
                        <h4 class="font-medium text-gray-900">Reporte Diario</h4>
                        <p class="text-sm text-gray-500 mt-1">Detalle de todas las actividades del día actual.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Instrucciones</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>• Usa los botones en la parte superior para exportar diferentes tipos de reportes</p>
                        <p>• Para exportar registros de tablas específicas, ve a las secciones correspondientes (Órdenes, Productos, etc.)</p>
                        <p>• Los archivos se descargarán automáticamente en formato Excel (.xlsx)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
