<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $customer = Role::firstOrCreate(['name' => 'customer']);

        $user = User::firstOrCreate(
            ['email' => 'admin@beauty.test'],
            ['name' => 'Shop Admin', 'password' => Hash::make('password')]
        );
        $user->assignRole($admin);
    }
}
