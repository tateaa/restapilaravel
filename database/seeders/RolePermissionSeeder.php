<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guard = 'web'; // Default guard untuk permission system

        $permissions = [
            'posts:read', 'posts:write', 'posts:delete',
            'comments:read', 'comments:write', 'comments:delete',
        ];

        foreach ($permissions as $perm) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $perm, 'guard_name' => $guard]);
        }

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard])
            ->syncPermissions(\Spatie\Permission\Models\Permission::all());

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'editor', 'guard_name' => $guard])
            ->syncPermissions(['posts:read', 'posts:write', 'comments:read', 'comments:write']);

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'reader', 'guard_name' => $guard])
            ->syncPermissions(['posts:read', 'comments:read']);
    }
}
