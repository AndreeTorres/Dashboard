<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                            <dd class="text-lg font-medium text-gray-900">HNL {{ number_format(\App\Models\Order::sum('total'), 2) }}</dd>
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
                            <dd class="text-lg font-medium text-gray-900">HNL {{ number_format(\App\Models\Expense::sum('amount'), 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Opciones de Exportación</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Exporta diferentes tipos de reportes y estadísticas en formato Excel.
                </p>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900">Estadísticas Completas</h4>
                        <p class="text-sm text-gray-500 mt-1">Exporta todas las estadísticas del dashboard incluyendo ventas, gastos y productos más vendidos.</p>
                    </div>
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900">Reporte Diario</h4>
                        <p class="text-sm text-gray-500 mt-1">Exporta un resumen detallado de las actividades del día actual.</p>
                    </div>
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900">Reporte Mensual</h4>
                        <p class="text-sm text-gray-500 mt-1">Exporta estadísticas y análisis del mes actual con ventas por día.</p>
                    </div>
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900">Datos de Tablas</h4>
                        <p class="text-sm text-gray-500 mt-1">Ve a las secciones específicas (Órdenes, Productos, etc.) para exportar registros individuales.</p>
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
                        <p>• Usa los botones en la parte superior para exportar reportes completos</p>
                        <p>• Para exportar registros específicos, ve a las secciones correspondientes y selecciona los elementos</p>
                        <p>• Los archivos se descargarán automáticamente en formato Excel (.xlsx)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
