<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Models\Product;
use App\Models\ProductCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderDetails';

    protected static ?string $title = 'Order detail';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __(static::$title);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label(__('Product'))
                    ->options(
                        ProductCategory::query()
                            ->with([
                                'subcategories.products' => fn ($query) => $query->available()->orderBy('name'),
                            ])
                            ->get()
                            ->flatMap(function (ProductCategory $category) {
                                return $category->subcategories->flatMap(function ($subcategory) use ($category) {
                                    $group = $category->name.' - '.$subcategory->name;

                                    return $subcategory->products->isNotEmpty()
                                        ? [$group => $subcategory->products->pluck('name', 'id')->toArray()]
                                        : [];
                                });
                            })->toArray()
                    )
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $product = Product::find($get('product_id'), ['id', 'price']);
                        if ($product) {
                            $set('unit_price', $product->price);
                            $this->calculateSubtotal($get, $set);
                        } else {
                            $set('unit_price', 0.00);
                        }
                    })
                    ->required()
                    ->preload()
                    ->searchable()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('quantity')
                    ->label(__('Quantity'))
                    ->prefixAction(
                        fn (Get $get, Set $set) => Forms\Components\Actions\Action::make('decrement')
                            ->icon('heroicon-o-minus')
                            ->action(function () use ($get, $set) {
                                $newQuantity = max(1, $get('quantity') - 1);
                                $set('quantity', $newQuantity);
                                $this->calculateSubtotal($get, $set);
                            })
                            ->color('primary')
                            ->disabled(fn () => $get('quantity') <= 1)
                    )
                    ->suffixAction(
                        fn (Get $get, Set $set) => Forms\Components\Actions\Action::make('increment')
                            ->icon('heroicon-o-plus')
                            ->action(function () use ($get, $set) {
                                $newQuantity = $get('quantity') + 1;
                                $set('quantity', $newQuantity);
                                $this->calculateSubtotal($get, $set);
                            })
                            ->color('primary')
                    )
                    ->numeric()
                    ->required()
                    ->default(1)
                    ->minValue(1)
                    ->columnSpanFull(),

                Forms\Components\Hidden::make('unit_price'),
                Forms\Components\TextInput::make('unit_price')
                    ->label(__('Unit price'))
                    ->numeric()
                    ->required()
                    ->default(0.00)
                    ->step(0.01)
                    ->disabled()
                    ->columnSpan([
                        'default' => 12,
                        'sm' => 12,
                        'md' => 12,
                        'lg' => 4,
                    ]),

                Forms\Components\TextInput::make('discount_percentage')
                    ->label(__('Discount percentage'))
                    ->numeric()
                    ->required()
                    ->default(0.00)
                    ->step(0.01)
                    ->live()
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->calculateSubtotal($get, $set))
                    ->columnSpan([
                        'default' => 12,
                        'sm' => 12,
                        'md' => 12,
                        'lg' => 4,
                    ]),

                Forms\Components\TextInput::make('discount')
                    ->label(__('Discount'))
                    ->numeric()
                    ->default(0.00)
                    ->step(0.01)
                    ->live()
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->calculateSubtotal($get, $set))
                    ->columnSpan([
                        'default' => 12,
                        'sm' => 12,
                        'md' => 12,
                        'lg' => 4,
                    ]),

                Forms\Components\Hidden::make('subtotal'),
                Forms\Components\TextInput::make('subtotal')
                    ->label(__('Subtotal'))
                    ->numeric()
                    ->default(0.00)
                    ->step(0.01)
                    ->disabled()
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('notes')
                    ->label(__('Notes'))
                    ->maxLength(500)
                    ->rows(5)
                    ->columnSpanFull(),
            ])
            ->columns(12);
    }

    public function calculateSubtotal(Get $get, Set $set): void
    {
        $discountPercentage = $get('discount_percentage') ?? 0.00;
        if ($discountPercentage < 0 || $discountPercentage > 100) {
            $discountPercentage = 0.00;
            $set('discount_percentage', $discountPercentage);
        }

        $quantity = $get('quantity');
        $unitPrice = $get('unit_price');
        if (empty($unitPrice) || empty($quantity)) {
            $set('discount', 0.00);
            $set('subtotal', 0.00);

            return;
        }

        $discount = ($unitPrice * $quantity) * ($discountPercentage / 100);
        $set('discount', round($discount, 2));

        $subtotal = ($unitPrice * $quantity) - $discount;
        $set('subtotal', max(0, round($subtotal, 2)));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product.name')
            ->columns([
                Tables\Columns\ImageColumn::make('product.image')
                    ->label(__('Image'))
                    ->disk('public'),

                Tables\Columns\TextColumn::make('product.subcategory.category.name')
                    ->label(__('Category'))
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('product.subcategory.name')
                    ->label(__('Subcategory'))
                    ->description(fn ($record) => $record->subcategory?->category?->name ?? null)
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('Name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label(__('Unit price'))
                    ->money('HNL')
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('Quantity'))
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount')
                    ->label(__('Discount'))
                    ->description(fn ($record) => $record->discount_percentage.'%')
                    ->money('HNL')
                    ->summarize([
                        Summarizers\Sum::make()->money('HNL'),
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('subtotal')
                    ->label(__('Subtotal'))
                    ->money('HNL')
                    ->summarize([
                        Summarizers\Sum::make()->money('HNL'),
                    ])
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Add product'))
                    ->icon('heroicon-o-plus')
                    ->modalHeading(__('Add product'))
                    ->modalSubmitActionLabel(__('Add'))
                    ->createAnother(false)
                    ->successRedirectUrl(fn () => $this->refreshOwnerRecord()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->successRedirectUrl(fn () => $this->refreshOwnerRecord()),
                Tables\Actions\DeleteAction::make()->successRedirectUrl(fn () => $this->refreshOwnerRecord()),
                Tables\Actions\RestoreAction::make()->successRedirectUrl(fn () => $this->refreshOwnerRecord()),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'product.subcategory.category',
            ])->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }

    private function refreshOwnerRecord(): void
    {
        /**
         * Second parameter is the first parameter of the method what `dispatch` is calling.
         * This will trigger the `refreshFormData` method in the `EditOrder` page, heredated by `EditRecord` class.
         *
         * The information of the order was updated through the `OrderDetailObserver` class,
         */
        $this->dispatch('refreshOwnerRecord', [
            'subtotal',
            'tax',
            'tipping_percentage',
            'tipping',
            'total',
        ]);

        Notification::make()
            ->title(__('Total updated successfully.'))
            ->success()
            ->send();
    }
}
