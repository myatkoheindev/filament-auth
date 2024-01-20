<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // USER MODEL
        $userCreatePermission = Permission::create(['name' => 'Create User', 'slug' => 'create-user', 'type' => 'User']);
        $userReadPermission = Permission::create(['name' => 'Read User', 'slug' => 'read-user', 'type' => 'User']);
        $userUpdatePermission = Permission::create(['name' => 'Update User', 'slug' => 'update-user', 'type' => 'User']);
        $userDeletePermission = Permission::create(['name' => 'Delete User', 'slug' => 'delete-user', 'type' => 'User']);

        // ROLE MODEL
        $roleCreatePermission = Permission::create(['name' => 'Create Role', 'slug' => 'create-role', 'type' => 'Role']);
        $roleReadPermission = Permission::create(['name' => 'Read Role', 'slug' => 'read-role', 'type' => 'Role']);
        $roleUpdatePermission = Permission::create(['name' => 'Update Role', 'slug' => 'update-role', 'type' => 'Role']);
        $roleDeletePermission = Permission::create(['name' => 'Delete Role', 'slug' => 'delete-role', 'type' => 'Role']);

        // PERMISSION MODEL
        $permissionCreatePermission = Permission::create(['name' => 'Create Permission', 'slug' => 'create-permission', 'type' => 'Permission']);
        $permissionReadPermission = Permission::create(['name' => 'Read Permission', 'slug' => 'read-permission', 'type' => 'Permission']);
        $permissionUpdatePermission = Permission::create(['name' => 'Update Permission', 'slug' => 'update-permission', 'type' => 'Permission']);
        $permissionDeletePermission = Permission::create(['name' => 'Delete Permission', 'slug' => 'delete-permission', 'type' => 'Permission']);
        
        $adminRole = Role::create(['name' => 'Admin', 'slug' => 'admin']);

        $adminRole->permissions()
                ->sync([
                    $userCreatePermission->id,
                    $userReadPermission->id,
                    $userUpdatePermission->id,
                    $userDeletePermission->id,
                    $roleCreatePermission->id,
                    $roleReadPermission->id,
                    $roleUpdatePermission->id,
                    $roleDeletePermission->id,
                    $permissionCreatePermission->id,
                    $permissionReadPermission->id,
                    $permissionUpdatePermission->id,
                    $permissionDeletePermission->id,
        ]);
        
        $now = Carbon::now()->toDateTimeString();

        $user1 = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'created_at' => $now,
            'updated_at' => $now
        ]);

        $user1->roles()->sync($adminRole->id);
        
    }
}
