<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class UserDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // Call the comprehensive ShipCentral user seeder
        $this->call(ShipCentralUserSeeder::class);

        // Legacy setup for backward compatibility (optional)
        $this->setupLegacyUsers();
    }

    /**
     * Setup legacy users for backward compatibility
     */
    private function setupLegacyUsers()
    {
        $now = DB::raw('NOW()');
        
        // Ensure legacy superadmin exists
        $legacyAdmin = DB::table('users')->where('email', 'admin@bhr.com')->first();
        if (!$legacyAdmin) {
            $userData = [
                'name' => 'Legacy Superadmin',
                'group_id' => 1,
                'username' => 'admin',
                'email'    => 'admin@bhr.com',
                'password' => Hash::make('admin'),
                'avatar'   => '',
                'role'   => '',
                'locale'   => 'th',
                'status'   => 1,
                'api_enable'   => 1,
                'last_logedin_at' => $now,
                'remember_token' => '',
                'created_at' => $now,
                'updated_at' => $now
            ];

            // Add ShipCentral fields if they exist (from enhanced migration)
            if (Schema::hasColumn('users', 'user_type')) {
                $userData['user_type'] = 'company_admin';
            }
            if (Schema::hasColumn('users', 'is_active')) {
                $userData['is_active'] = true;
            }

            DB::table('users')->insert($userData);
        }

        // Setup user groups
        DB::table('user_groups')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'Company Admin Group',
                'description' => 'Company administrators with full system access',
                'default_role' => 'company_admin',
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ]
        );
        
        DB::table('user_groups')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'Branch Admin Group',
                'description' => 'Branch administrators with branch-level access',
                'default_role' => 'branch_admin',
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ]
        );

        DB::table('user_groups')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'Branch Staff Group',
                'description' => 'Branch staff with operational access',
                'default_role' => 'branch_staff',
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ]
        );
    }
}
