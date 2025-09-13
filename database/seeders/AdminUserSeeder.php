<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name'     => 'System Administrator',
                'username' => 'admin',
                'email'    => 'admin@example.com',
                'password' => Hash::make('123'),
                'role'     => 'admin',
            ]
        );
    }
}
