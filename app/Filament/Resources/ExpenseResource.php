<?php

namespace App\Filament\Resources;

use App\Enums\MeasureUnitEnum;
use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\Widgets;
use App\Models\Expense;
use App\Models\Supply;
use App\Models\SupplyCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    public static function getModelLabel(): string
    {
        return __('Expense');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Expenses');
    }

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('supply_id')
                        ->label(__('Supply'))
                        ->options(
                            SupplyCategory::query()
                                ->with('supplies')
                                ->get()
                                ->mapWithKeys(function (SupplyCategory $category) {
                                    if ($category->supplies->isNotEmpty()) {
                                        return [
                                            $category->name => $category->supplies->pluck('name', 'id')->toArray(),
                                        ];
                                    }

                                    return [];
                                })
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->createOptionForm([
                            Forms\Components\Select::make('supply_category_id')
                                ->label(__('Supply category'))
                                ->options(
                                    SupplyCategory::query()
                                        ->pluck('name', 'id')
                                )
                                ->required()
                                ->searchable()
                                ->preload()
                                ->columnSpan([
                                    'default' => 12,
                                    'lg' => 4,
                                ]),

                            Forms\Components\TextInput::make('name')
                                ->label(__('Name'))
                                ->required()
                                ->maxLength(255)
                                ->columnSpan([
                                    'default' => 12,
                                    'lg' => 4,
                                ]),

                            Forms\Components\Textarea::make('description')
                                ->label(__('Description'))
                                ->maxLength(255)
                                ->columnSpanFull(),
                        ])
                        ->createOptionUsing(fn(array $data) => Supply::create([
                            'supply_category_id' => $data['supply_category_id'],
                            'name' => $data['name'],
                            'description' => $data['description'] ?? null,
                        ])->id)
                        ->columns(2)
                        ->columnSpan([
                            'default' => 12,
                            'md' => 6,
                            'lg' => 4,
                            'xl' => 3,
                        ]),

                    Forms\Components\TextInput::make('cost')
                        ->label(__('Cost'))
                        ->hintIcon('heroicon-o-information-circle', __('Total cost of the supply.'))
                        ->required()
                        ->numeric()
                        ->default(0.00)
                        ->prefix('L')
                        ->step(0.01)
                        ->columnSpan([
                            'default' => 12,
                            'md' => 6,
                            'lg' => 2,
                            'xl' => 2,
                        ]),

                    Forms\Components\TextInput::make('quantity')
                        ->label(__('Quantity'))
                        ->hintIcon('heroicon-o-information-circle', __('Quantity of the supply.'))
                        ->required()
                        ->numeric()
                        ->default(1.00)
                        ->step(0.01)
                        ->columnSpan([
                            'default' => 12,
                            'md' => 4,
                            'lg' => 2,
                            'xl' => 2,
                        ]),

                    Forms\Components\Select::make('measure_unit')
                        ->label(__('Measure unit'))
                        ->options(MeasureUnitEnum::toSelectOptions())
                        ->native(false)
                        ->required()
                        ->columnSpan([
                            'default' => 12,
                            'md' => 4,
                            'lg' => 2,
                            'xl' => 3,
                        ]),

                    Forms\Components\DatePicker::make('date')
                        ->label(__('Date'))
                        ->required()
                        ->default(now())
                        ->columnSpan([
                            'default' => 12,
                            'md' => 4,
                            'lg' => 2,
                            'xl' => 2,
                        ]),

                    Forms\Components\Textarea::make('notes')
                        ->label(__('Notes'))
                        ->maxLength(500)
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                            'xl' => 12,
                        ]),
                ])->columns(12),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('supply.name')
                    ->label(__('Supply'))
                    ->description(fn($record) => $record->supply?->supplyCategory?->name ?? null)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('cost')
                    ->label(__('Cost'))
                    ->money('HNL')
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('Quantity'))
                    ->description(
                        fn(Expense $record) => MeasureUnitEnum::tryFrom($record->measure_unit?->value)?->translatedLabel(plural: $record->quantity > 1)
                    )
                    ->badge()
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->date('F j, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label(__('Notes'))
                    ->tooltip(fn($state) => strlen($state) > 30 ? $state : null)
                    ->limit(30)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('F j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('F j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime('F j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('supply_id')
                    ->label(__('Supply'))
                    ->options(
                        SupplyCategory::query()
                            ->with('supplies')
                            ->get()
                            ->mapWithKeys(function (SupplyCategory $category) {
                                if ($category->supplies->isNotEmpty()) {
                                    return [
                                        $category->name => $category->supplies->pluck('name', 'id')->toArray(),
                                    ];
                                }

                                return [];
                            })
                    )
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->columnSpan([
                        'default' => 6,
                        'sm' => 3,
                        'md' => 3,
                        'lg' => 3,
                        'xl' => 1,
                    ]),

                Tables\Filters\SelectFilter::make('supply.supply_category_id')
                    ->label(__('Supply category'))
                    ->relationship('supply.supplyCategory', 'name')
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->options(fn(Builder $query) => $query->pluck('name', 'id'))
                    ->columnSpan([
                        'default' => 6,
                        'sm' => 3,
                        'lg' => 3,
                        'xl' => 1,
                    ]),

                Tables\Filters\SelectFilter::make('measure_unit')
                    ->label(__('Measure unit'))
                    ->options(MeasureUnitEnum::class)
                    ->multiple()
                    ->columnSpan([
                        'default' => 6,
                        'sm' => 3,
                        'md' => 3,
                        'lg' => 3,
                        'xl' => 1,
                    ]),

                DateRangeFilter::make('date')
                    ->defaultLast30Days()
                    ->columnSpan([
                        'default' => 6,
                        'sm' => 3,
                        'lg' => 3,
                        'xl' => 1,
                    ]),

                Tables\Filters\TrashedFilter::make()
                    ->columnSpan([
                        'default' => 6,
                        'sm' => 3,
                        'md' => 3,
                        'lg' => 3,
                        'xl' => 1,
                    ]),
            ])
            ->filtersFormColumns(6)
            ->filtersFormWidth(MaxWidth::SevenExtraLarge)
            ->filtersLayout(FiltersLayout::AboveContentCollapsible)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    ExportBulkAction::make(),
                ]),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\ExpenseStatsOverview::class,
            Widgets\ExpenseChartWidget::class,
        ];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'view' => Pages\ViewExpense::route('/{record}'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
