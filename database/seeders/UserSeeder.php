<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        $admin = User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Editor user
        $editor = User::create([
            'name'     => 'Budi Santoso',
            'email'    => 'budi@example.com',
            'password' => Hash::make('password'),
        ]);
        $editor->assignRole('editor');

        // Regular users (readers)
        $users = [
            [
                'name'     => 'Siti Rahayu',
                'email'    => 'siti@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name'     => 'Agus Wijaya',
                'email'    => 'agus@example.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $user) {
            $u = User::create($user);
            $u->assignRole('reader');
        }
    }
}
