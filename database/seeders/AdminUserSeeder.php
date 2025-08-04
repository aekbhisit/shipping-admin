<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\User\Entities\Users;
use Modules\User\Entities\Roles;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create admin role if it doesn't exist
        $adminRole = Roles::firstOrCreate([
            'name' => 'admin'
        ], [
            'name' => 'admin',
            'all' => 1,
            'module' => 'all',
            'group' => 'admin',
            'permissions' => '[]',
            'sequence' => 1,
            'status' => 1
        ]);

        // Create test admin user
        $adminUser = Users::firstOrCreate([
            'username' => 'admin'
        ], [
            'role_id' => $adminRole->id,
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('123456'),
            'status' => 1,
            'api' => 1
        ]);

        // Create branch admin user
        $branchRole = Roles::firstOrCreate([
            'name' => 'branch_admin'
        ], [
            'name' => 'branch_admin',
            'all' => 0,
            'module' => 'branch',
            'group' => 'branch',
            'permissions' => '[]',
            'sequence' => 2,
            'status' => 1
        ]);

        $branchUser = Users::firstOrCreate([
            'username' => 'branch'
        ], [
            'role_id' => $branchRole->id,
            'name' => 'Branch Admin',
            'username' => 'branch',
            'email' => 'branch@test.com',
            'password' => Hash::make('123456'),
            'status' => 1,
            'api' => 1
        ]);

        $this->command->info('Test users created:');
        $this->command->info('Admin User - username: admin, password: 123456');
        $this->command->info('Branch User - username: branch, password: 123456');
    }
} 