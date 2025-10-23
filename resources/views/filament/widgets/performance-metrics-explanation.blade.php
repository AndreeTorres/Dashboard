<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('Performance Radar Chart Guide') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Understanding the Business Performance Metrics radar chart') }}
        </x-slot>

        <div class="space-y-4">
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    {{ __('The radar chart displays 6 key business metrics on a 0-100% scale. The larger the highlighted area, the better your overall business performance. Each point on the chart represents a different aspect of your business:') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($this->getViewData()['metrics'] as $metric)
                    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                @php
                                    $colorClasses = [
                                        'success' => 'text-green-500',
                                        'warning' => 'text-yellow-500',
                                        'info' => 'text-blue-500',
                                        'primary' => 'text-indigo-500',
                                        'danger' => 'text-red-500',
                                    ];
                                @endphp
                                <x-heroicon-o-chart-bar-square class="w-6 h-6 {{ $colorClasses[$metric['color']] ?? 'text-gray-500' }}" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $metric['name'] }}
                                </h4>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $metric['description'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mt-4">
                <div class="flex items-center space-x-2">
                    <x-heroicon-o-information-circle class="w-5 h-5 text-blue-500" />
                    <h5 class="text-sm font-medium text-blue-900 dark:text-blue-100">
                        {{ __('How to Read the Chart') }}
                    </h5>
                </div>
                <ul class="text-xs text-blue-800 dark:text-blue-200 mt-2 space-y-1 ml-7">
                    <li>• {{ __('Values closer to the center (0%) indicate areas needing improvement') }}</li>
                    <li>• {{ __('Values closer to the edge (100%) show strong performance areas') }}</li>
                    <li>• {{ __('A larger filled area indicates better overall business health') }}</li>
                    <li>• {{ __('All metrics are calculated from the last 30 days of data') }}</li>
                </ul>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
