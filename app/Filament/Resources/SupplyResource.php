<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplyResource\Pages;
use App\Models\Supply;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class SupplyResource extends Resource
{
    protected static ?string $model = Supply::class;

    public static function getModelLabel(): string
    {
        return __('Supply');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Supplies');
    }

    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('supply_category_id')
                        ->label(__('Supply category'))
                        ->relationship('supplyCategory', 'name')
                        ->required()
                        ->preload()
                        ->searchable()
                        ->columnSpan(12),

                    Forms\Components\TextInput::make('name')
                        ->label(__('Name'))
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(12),

                    Forms\Components\Textarea::make('description')
                        ->label(__('Description'))
                        ->maxLength(255)
                        ->columnSpan(12),
                ])->columns(12),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('supplyCategory.name')
                    ->label(__('Supply category'))
                    ->badge()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label(__('Description'))
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
                Tables\Filters\SelectFilter::make('supply_category_id')
                    ->label(__('Supply category'))
                    ->relationship('supplyCategory', 'name')
                    ->searchable(),

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
            'index' => Pages\ListSupplies::route('/'),
            'create' => Pages\CreateSupply::route('/create'),
            'view' => Pages\ViewSupply::route('/{record}'),
            'edit' => Pages\EditSupply::route('/{record}/edit'),
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
