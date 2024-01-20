<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Role;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Permission;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\CheckboxList;
use App\Filament\Resources\RoleResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RoleResource\RelationManagers;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $permissions = Permission::select('type', 'id', 'name')
            ->groupBy('type', 'id', 'name')
            ->get();

        $types = [];

        foreach ($permissions as $permission) {
            $types[$permission->type][] = [
                'id' => $permission->id,
                'name' => $permission->name,
            ];
        }

        $checkboxLists = [];

        foreach ($types as $type => $actions) {
            $options = [];

            foreach ($actions as $action) {
                $options[$action['id']] = $action['name'];
            }

            $checkboxLists[] = CheckboxList::make('permissions')
                ->options($options)
                ->label($type)
                ->bulkToggleable();
        }

        $form->schema([
            Section::make()
                ->schema([
                    TextInput::make('name')
                        ->label('Role Name')
                        ->required()
                        ->filled()
                        ->placeholder('Enter a role name')
                        ->live(debounce: 1000)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                        ->columnSpan(2),
                    TextInput::make('slug')
                        ->disabled()
                        ->placeholder('Auto Generate Role Name')
                        ->columnSpan(2),
                    Hidden::make('slug'),
                    Section::make('permissions')
                        ->label('Permissions')
                        ->schema($checkboxLists)
                        ->columns(4)
                ])
                ->columns(4)
        ]);

        return $form;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('No')
                    ->rowIndex()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Role Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('permissions_count')
                    ->badge()
                    ->label('Permissions')
                    ->counts('permissions')
            ])
            ->filters([
                //
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
