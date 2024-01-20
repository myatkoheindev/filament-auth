<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Models\Role;
use Filament\Actions;
use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        $record->permissions()->sync($data['permissions']);

        return $record; 
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $role = Role::with('permissions')->where('slug', $data['slug'])->first();
        // dd($role);

        $permissions = $role->permissions()->get()->pluck('id');
        $data['permissions'] = $permissions;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return "Role updated successfully";
    }
}
