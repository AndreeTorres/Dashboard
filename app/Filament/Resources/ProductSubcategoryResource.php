<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductSubcategoryResource\Pages;
use App\Models\ProductSubcategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ProductSubcategoryResource extends Resource
{
    protected static ?string $model = ProductSubcategory::class;

    public static function getModelLabel(): string
    {
        return __('Subcategory');
    }

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('product_category_id')
                        ->label('Category')
                        ->relationship('category', 'name')
                        ->required()
                        ->preload()
                        ->searchable()
                        ->columnSpan(12),

                    Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(12),

                    Forms\Components\Textarea::make('description')
                        ->label('description')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(12),
                ])->columns(12),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('category.name')
            ->groups([
                Group::make('category.name')
                    ->label(__('Category')),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->tooltip(fn ($state) => strlen($state) > 50 ? $state : null)
                    ->limit(50)
                    ->sortable()
                    ->searchable(),

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
                Tables\Filters\SelectFilter::make('product_category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->multiple()
                    ->preload(),

                Tables\Filters\TrashedFilter::make(),
            ])
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
                ]),
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
            'index' => Pages\ListProductSubcategories::route('/'),
            'create' => Pages\CreateProductSubcategory::route('/create'),
            'view' => Pages\ViewProductSubcategory::route('/{record}'),
            'edit' => Pages\EditProductSubcategory::route('/{record}/edit'),
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
