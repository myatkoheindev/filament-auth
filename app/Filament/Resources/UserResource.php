<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Role;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Rawilk\FilamentPasswordInput\Password;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\RelationManagers;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                TextInput::make('name')
                    ->required()
                    ->filled()
                    ->placeholder('Enter your name')
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->filled()
                    ->placeholder('Enter your email')
                    ->maxLength(255),
                
                // Select::make('roles')
                //     ->relationship('roles', 'name')
                //     ->helperText('Only Choose One!')
                //     ->required(),
                Select::make('roles')
                    ->multiple()
                    ->searchable()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->required(),

                Password::make('password')
                    ->placeholder('Enter your password')
                    ->password()
                    ->regeneratePassword()
                    ->passwordRegeneratedMessage('New password was generated!')
                    ->regeneratePasswordIconColor('primary')
                    ->generatePasswordUsing(function ($state) {
                        $length = 12; // Set your desired password length

                        // Character sets
                        $upperLowerSet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        $specialCharacterSet = '@#$%^&*()';
                        $digitSet = '0123456789';

                        // Initialize password
                        $password = '';

                        // Alternate uppercase and lowercase
                        $upper = true;

                        for ($i = 0; $i < $length; $i++) {
                            switch ($i % 3) {
                                case 0:
                                    // Uppercase or lowercase
                                    $password .= $upper ? strtoupper($upperLowerSet[rand(0, strlen($upperLowerSet) - 1)]) :
                                        strtolower($upperLowerSet[rand(0, strlen($upperLowerSet) - 1)]);
                                    $upper = !$upper;
                                    break;
                                case 1:
                                    // Special character
                                    $password .= $specialCharacterSet[rand(0, strlen($specialCharacterSet) - 1)];
                                    break;
                                case 2:
                                    // Digit
                                    $password .= $digitSet[rand(0, strlen($digitSet) - 1)];
                                    break;
                            }
                        }

                        return $password;
                    })
                    ->hiddenOn('view')
                    ->maxLength(255)
                    ->dehydrateStateUsing(static fn(null|string $state):
                        null|string =>
                        filled($state) ? Hash::make($state): null,
                    )->required(static function (Page $livewire): bool{
                        return $livewire instanceof CreateUser;
                    })->dehydrated(static fn(null|string $state): bool =>
                        filled($state),
                    )->label(static fn(Page $livewire): string =>
                        ($livewire instanceof EditUser) ? 'New Password': 'Password'
                    ),
                FileUpload::make('image')
                    ->disk('public')
                    ->directory('user_image')
                    ->visibility('private')
                    
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('roles.name')
                    ->badge()
                    ->label('Role'),
            ])
            ->filters([
                //
                SelectFilter::make('name')
                    ->label('Role')
                    ->multiple()
                    ->options(Role::pluck('name', 'name'))
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                ])
                ->label('Actions')
                ->size(ActionSize::Small)
                ->color('primary')
                ->button()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
