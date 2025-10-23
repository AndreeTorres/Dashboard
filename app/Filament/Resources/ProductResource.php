<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\ProductCategory;
use CodeWithDennis\FilamentPriceFilter\Filament\Tables\Filters\PriceFilter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    public static function getModelLabel(): string
    {
        return __('Product');
    }

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('Name'))
                        ->required()
                        ->maxLength(255)
                        ->columnSpan([
                            'default' => 12,
                            'sm' => 6,
                            'md' => 6,
                            'lg' => 4,
                        ]),

                    Forms\Components\Select::make('product_subcategory_id')
                        ->label(__('Subcategory'))
                        ->options(
                            ProductCategory::with('subcategories')
                                ->get()
                                ->mapWithKeys(function (ProductCategory $category) {
                                    if ($category->subcategories->isNotEmpty()) {
                                        return [
                                            $category->name => $category->subcategories->pluck('name', 'id')->toArray(),
                                        ];
                                    }

                                    return [];
                                })
                                ->toArray()
                        )
                        ->live()
                        ->preload()
                        ->searchable()
                        ->required()
                        ->columnSpan([
                            'default' => 12,
                            'sm' => 6,
                            'md' => 6,
                            'lg' => 4,
                        ]),

                    Forms\Components\TextInput::make('price')
                        ->label(__('Price'))
                        ->prefix('L')
                        ->numeric()
                        ->required()
                        ->columnSpan([
                            'default' => 12,
                            'sm' => 6,
                            'md' => 6,
                            'lg' => 2,
                        ]),

                    Forms\Components\Toggle::make('available')
                        ->label(__('Available'))
                        ->inline(false)
                        ->columnSpan([
                            'default' => 12,
                            'sm' => 6,
                            'md' => 6,
                            'lg' => 2,
                        ]),

                    Forms\Components\Textarea::make('description')
                        ->label(__('Description'))
                        ->columnSpan(12),

                    Forms\Components\FileUpload::make('image')
                        ->label(__('Image'))
                        ->image()
                        ->directory('products')
                        ->disk('public')
                        ->columnSpan(12),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('subcategory.category.name')
            ->groups([
                Group::make('subcategory.category.name')
                    ->label(__('Category')),
            ])
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label(__('Image'))
                    ->disk('public'),

                Tables\Columns\TextColumn::make('subcategory.category.name')
                    ->label(__('Category'))
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('subcategory.name')
                    ->label(__('Subcategory'))
                    ->description(fn ($record) => $record->subcategory?->category?->name ?? null)
                    ->badge()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('price')
                    ->label(__('Price'))
                    ->money('HNL')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('available')
                    ->label(__('Available'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->tooltip(fn ($state) => strlen($state) > 50 ? $state : null)
                    ->limit(50)
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

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
                PriceFilter::make('price'),

                Tables\Filters\SelectFilter::make('product_category_id')
                    ->label(__('Category'))
                    ->options(ProductCategory::pluck('name', 'id'))
                    ->multiple()
                    ->searchable()
                    ->columnSpan(2),

                Tables\Filters\SelectFilter::make('product_subcategory_id')
                    ->label(__('Subcategory'))
                    ->options(
                        ProductCategory::with('subcategories')
                            ->get()
                            ->mapWithKeys(function ($category) {
                                if ($category->subcategories->isNotEmpty()) {
                                    return [
                                        $category->name => $category->subcategories->pluck('name', 'id')->toArray(),
                                    ];
                                }

                                return [];
                            })
                            ->toArray()
                    )
                    ->multiple()
                    ->searchable()
                    ->columnSpan(2),

                Tables\Filters\SelectFilter::make('available')
                    ->label(__('Available'))
                    ->options([
                        '1' => __('Yes'),
                        '0' => __('No'),
                    ])
                    ->default('1')
                    ->columnSpan(2),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
            ]);
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
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
