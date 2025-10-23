<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TableResource\Pages;
use App\Models\Table as TableModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class TableResource extends Resource
{
    protected static ?string $model = TableModel::class;

    public static function getModelLabel(): string
    {
        return __('Table');
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan([
                            'default' => 12,
                            'sm' => 8,
                            'sm' => 6,
                            'lg' => 10,
                        ]),

                    Forms\Components\Toggle::make('available')
                        ->label(__('Available'))
                        ->inline(false)
                        ->columnSpan([
                            'default' => 12,
                            'sm' => 4,
                            'sm' => 6,
                            'lg' => 2,
                        ]),

                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->helperText(__('Ideal for providing additional details about the table, such as its location or special features.'))
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(12),
                ])->columns(12),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('available')
            ->groups([
                Group::make('available')
                    ->label(__('Available'))
                    ->getTitleFromRecordUsing(fn ($record) => $record->available ? __('Yes') : __('No')),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('markAsAvailable')
                        ->label(__('Mark as available'))
                        ->action(function ($records) {
                            TableModel::whereIn('id', $records->pluck('id'))
                                ->update(['available' => true]);

                            Notification::make()
                                ->title(__('Tables marked as available'))
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->icon('heroicon-o-check'),
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
            'index' => Pages\ListTables::route('/'),
            'create' => Pages\CreateTable::route('/create'),
            'view' => Pages\ViewTable::route('/{record}'),
            'edit' => Pages\EditTable::route('/{record}/edit'),
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
