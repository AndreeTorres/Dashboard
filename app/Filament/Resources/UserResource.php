<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use DragonCode\Support\Facades\Helpers\Str;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Rawilk\FilamentPasswordInput\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getModelLabel(): string
    {
        return __('User');
    }

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->columnSpan(4),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->rule('regex:/^[^\s@]+@[^\s@]+\.[^\s@]+$/')
                        ->columnSpan(4),

                    Password::make('password')
                        ->label('Password')
                        ->required()
                        ->copyable()
                        ->revealable()
                        ->regeneratePassword(
                            using: fn (Set $set) => $set('password', Str::random(12)),
                        )
                        ->visibleOn([
                            'create',
                        ])
                        ->columnSpan(4),

                    Forms\Components\Select::make('roles')
                        ->label('Roles')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->relationship('roles', 'name')
                        ->columnSpan(6),
                ])->columns(12),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage(__('Email address copied'))
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->listWithLineBreaks(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('F j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('F j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->authorize(fn (User $record) => $record->id !== Auth::id()),
                Tables\Actions\EditAction::make()->authorize(fn (User $record) => $record->id !== Auth::id()),

                Tables\Actions\Action::make('change_password')
                    ->label(__('Change Password'))
                    ->icon('heroicon-o-key')
                    ->color(Color::Orange)
                    ->form([
                        Password::make('password')
                            ->label('New Password')
                            ->required()
                            ->copyable()
                            ->revealable()
                            ->regeneratePassword(
                                using: fn (Set $set) => $set('password', Str::random(12)),
                            ),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->update([
                            'password' => bcrypt($data['password']),
                        ]);
                    })
                    ->authorize(fn (User $record) => $record->id !== Auth::id()),

                Tables\Actions\DeleteAction::make()->authorize(fn (User $record) => $record->id !== Auth::id()),
                Tables\Actions\RestoreAction::make()->authorize(fn (User $record) => $record->id !== Auth::id()),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('id', '>', 1);  // Exclude the default user with ID 1
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
