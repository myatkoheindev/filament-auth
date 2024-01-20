<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Models\Role;
use Filament\Actions;
use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\ViewRecord;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $role = Role::where('slug', $data['slug'])->first();
        $permissions = $role->permissions()->get()->pluck('id');
        $data['permissions'] = $permissions;
        return $data;
    }
}
