<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Models\Role;
use Filament\Actions;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Start Transaction
        DB::beginTransaction();
        try {
            $newRole = Role::create([
                'name' => $data['name'],
                'slug'  => $data['slug']
            ]);
            $newRole->permissions()->sync($data['permissions']);

            DB::commit();

            return $newRole;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return "Role created successfully";
    }
}
