<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ShipCentralUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Purpose: Create ShipCentral users with proper roles and permissions
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->command->info('🚀 Setting up ShipCentral Users with Roles & Permissions...');

        // Step 1: Ensure roles and permissions exist
        $this->ensureRolesAndPermissionsExist();

        // Step 2: Create sample branches (if Branch module exists)
        $branches = $this->createSampleBranches();

        // Step 3: Create Company Admin users
        $this->createCompanyAdmins();

        // Step 4: Create Branch Admin users
        $this->createBranchAdmins($branches);

        // Step 5: Create Branch Staff users
        $this->createBranchStaff($branches);

        // Step 6: Display summary
        $this->displaySummary();

        $this->command->info('✅ ShipCentral User setup completed successfully!');
    }

    /**
     * Ensure all required roles and permissions exist
     */
    private function ensureRolesAndPermissionsExist()
    {
        $this->command->info('📋 Ensuring roles and permissions exist...');

        // Create roles if they don't exist
        $roles = [
            'company_admin' => 'Company Administrator',
            'branch_admin' => 'Branch Administrator', 
            'branch_staff' => 'Branch Staff'
        ];

        foreach ($roles as $name => $displayName) {
            Role::firstOrCreate(
                ['name' => $name],
                ['guard_name' => 'web']
            );
        }

        // Enhanced permissions based on our plan
        $permissions = [
            // User Management
            'admin.user.company.index' => 'View All Company Users',
            'admin.user.company.create' => 'Create Company Users',
            'admin.user.company.edit' => 'Edit Company Users',
            'admin.user.company.delete' => 'Delete Company Users',
            
            'admin.user.branch.index' => 'View Branch Users',
            'admin.user.branch.create' => 'Create Branch Users',
            'admin.user.branch.edit' => 'Edit Branch Users',
            'admin.user.branch.delete' => 'Delete Branch Users',
            
            'admin.user.staff.index' => 'View Branch Staff',
            'admin.user.staff.create' => 'Create Branch Staff',
            'admin.user.staff.edit' => 'Edit Branch Staff',
            'admin.user.staff.delete' => 'Delete Branch Staff',

            // Branch Management
            'admin.branch.all.index' => 'View All Branches',
            'admin.branch.all.create' => 'Create Branches',
            'admin.branch.all.edit' => 'Edit Branches',
            'admin.branch.all.delete' => 'Delete Branches',
            'admin.branch.markups.index' => 'View Branch Markups',
            'admin.branch.markups.manage' => 'Manage Branch Markups',
            
            'admin.branch.own.index' => 'View Own Branch',
            'admin.branch.own.edit' => 'Edit Own Branch',
            'admin.branch.settings.edit' => 'Edit Branch Settings',

            // Customer Management
            'admin.customer.index' => 'View Customers',
            'admin.customer.create' => 'Create Customers',
            'admin.customer.edit' => 'Edit Customers',
            'admin.customer.delete' => 'Delete Customers',
            'admin.customer.import' => 'Import Customers',
            'admin.customer.export' => 'Export Customers',

            // Shipment Operations
            'admin.shipment.create' => 'Create Shipments',
            'admin.shipment.index' => 'View Shipments',
            'admin.shipment.edit' => 'Edit Shipments',
            'admin.shipment.delete' => 'Delete Shipments',
            'admin.shipment.track' => 'Track Shipments',
            'admin.shipment.all.index' => 'View All Shipments',
            'admin.shipment.branch.index' => 'View Branch Shipments',
            'admin.shipment.own.index' => 'View Own Shipments',

            // Carrier Management
            'admin.carrier.index' => 'View Carriers',
            'admin.carrier.create' => 'Create Carriers',
            'admin.carrier.edit' => 'Edit Carriers',
            'admin.carrier.delete' => 'Delete Carriers',
            'admin.carrier.credentials.index' => 'View Carrier Credentials',
            'admin.carrier.credentials.edit' => 'Edit Carrier Credentials',

            // Product Management
            'admin.product.index' => 'View Products',
            'admin.product.create' => 'Create Products',
            'admin.product.edit' => 'Edit Products',
            'admin.product.delete' => 'Delete Products',

            // Reports & Analytics
            'admin.reports.system.dashboard' => 'View System Dashboard',
            'admin.reports.branches.index' => 'View Branch Reports',
            'admin.reports.branches.comparison' => 'Compare Branches',
            'admin.reports.financial.index' => 'View Financial Reports',
            'admin.reports.branch.own' => 'View Own Branch Reports',

            // Billing & Export
            'admin.billing.all.index' => 'View All Billing',
            'admin.billing.branches.index' => 'View Branch Billing',
            'admin.billing.export.index' => 'Export Billing Data',
            'admin.billing.branch.own' => 'View Own Branch Billing',

            // Audit & System
            'admin.audit.index' => 'View Audit Logs',
            'admin.audit.system.view' => 'View System Audit',
            'admin.audit.branch.view' => 'View Branch Audit',
            'admin.system.settings' => 'System Settings',
            'admin.system.maintenance' => 'System Maintenance'
        ];

        foreach ($permissions as $name => $displayName) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['guard_name' => 'web']
            );
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();
    }

    /**
     * Create sample branches for testing
     */
    private function createSampleBranches()
    {
        $this->command->info('🏪 Creating sample branches...');

        $branches = [];
        $branchData = [
            [
                'name' => 'สาขากรุงเทพ',
                'code' => 'BKK01',
                'address' => '123 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110',
                'phone' => '02-123-4567',
                'email' => 'bangkok@shipcentral.com',
                'contact_person' => 'นายสมชาย ใจดี'
            ],
            [
                'name' => 'สาขาเชียงใหม่', 
                'code' => 'CNX01',
                'address' => '456 ถนนนิมมานเหมินท์ ตำบลสุเทพ อำเภอเมือง เชียงใหม่ 50200',
                'phone' => '053-123-456',
                'email' => 'chiangmai@shipcentral.com',
                'contact_person' => 'นางสาวสุดา เรียนดี'
            ],
            [
                'name' => 'สาขาภูเก็ต',
                'code' => 'HKT01', 
                'address' => '789 ถนนราษฎร์อุทิศ ตำบลปทุม อำเภอกะทู้ ภูเก็ต 83120',
                'phone' => '076-123-789',
                'email' => 'phuket@shipcentral.com',
                'contact_person' => 'นายวิชัย ทะเลใส'
            ]
        ];

        foreach ($branchData as $data) {
            // Check if Branch module exists and create branches
            if (class_exists('\Modules\Branch\Entities\Branch')) {
                $branch = \Modules\Branch\Entities\Branch::firstOrCreate(
                    ['code' => $data['code']],
                    array_merge($data, [
                        'is_active' => true,
                        'created_by' => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ])
                );
                $branches[] = $branch;
            } else {
                // Create in database directly if module doesn't exist yet
                $branchId = DB::table('branches')->insertGetId(array_merge($data, [
                    'is_active' => true,
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
                $branches[] = (object)['id' => $branchId, 'name' => $data['name']];
            }
        }

        return $branches;
    }

    /**
     * Create Company Admin users
     */
    private function createCompanyAdmins()
    {
        $this->command->info('👑 Creating Company Admin users...');

        $companyAdmins = [
            [
                'name' => 'ผู้ดูแลระบบหลัก',
                'email' => 'admin@shipcentral.com',
                'password' => 'admin123',
                'user_type' => User::USER_TYPE_COMPANY_ADMIN,
                'branch_id' => null, // Company admin has no branch assignment
            ],
            [
                'name' => 'CEO ShipCentral',
                'email' => 'ceo@shipcentral.com', 
                'password' => 'ceo123',
                'user_type' => User::USER_TYPE_COMPANY_ADMIN,
                'branch_id' => null,
            ]
        ];

        foreach ($companyAdmins as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'password' => Hash::make($userData['password']),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );

            // Assign company_admin role
            $user->assignRole('company_admin');
            
            $this->command->info("✅ Created Company Admin: {$user->name} ({$user->email})");
        }
    }

    /**
     * Create Branch Admin users
     */
    private function createBranchAdmins($branches)
    {
        $this->command->info('🏪 Creating Branch Admin users...');

        $branchAdminData = [
            [
                'name' => 'ผู้จัดการสาขากรุงเทพ',
                'email' => 'manager.bangkok@shipcentral.com',
                'password' => 'manager123',
                'branch_index' => 0
            ],
            [
                'name' => 'ผู้จัดการสาขาเชียงใหม่',
                'email' => 'manager.chiangmai@shipcentral.com',
                'password' => 'manager123', 
                'branch_index' => 1
            ],
            [
                'name' => 'ผู้จัดการสาขาภูเก็ต',
                'email' => 'manager.phuket@shipcentral.com',
                'password' => 'manager123',
                'branch_index' => 2
            ]
        ];

        foreach ($branchAdminData as $userData) {
            if (isset($branches[$userData['branch_index']])) {
                $branch = $branches[$userData['branch_index']];
                
                $user = User::firstOrCreate(
                    ['email' => $userData['email']],
                    [
                        'name' => $userData['name'],
                        'password' => Hash::make($userData['password']),
                        'user_type' => User::USER_TYPE_BRANCH_ADMIN,
                        'branch_id' => $branch->id,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );

                // Assign branch_admin role
                $user->assignRole('branch_admin');
                
                $this->command->info("✅ Created Branch Admin: {$user->name} for {$branch->name}");
            }
        }
    }

    /**
     * Create Branch Staff users
     */
    private function createBranchStaff($branches)
    {
        $this->command->info('👥 Creating Branch Staff users...');

        $staffData = [
            // Bangkok Branch Staff
            [
                'name' => 'พนักงานรับพัสดุ กรุงเทพ',
                'email' => 'staff1.bangkok@shipcentral.com',
                'password' => 'staff123',
                'branch_index' => 0
            ],
            [
                'name' => 'พนักงานจัดส่ง กรุงเทพ', 
                'email' => 'staff2.bangkok@shipcentral.com',
                'password' => 'staff123',
                'branch_index' => 0
            ],
            // Chiang Mai Branch Staff
            [
                'name' => 'พนักงานรับพัสดุ เชียงใหม่',
                'email' => 'staff1.chiangmai@shipcentral.com',
                'password' => 'staff123',
                'branch_index' => 1
            ],
            [
                'name' => 'พนักงานจัดส่ง เชียงใหม่',
                'email' => 'staff2.chiangmai@shipcentral.com',
                'password' => 'staff123',
                'branch_index' => 1
            ],
            // Phuket Branch Staff
            [
                'name' => 'พนักงานรับพัสดุ ภูเก็ต',
                'email' => 'staff1.phuket@shipcentral.com',
                'password' => 'staff123',
                'branch_index' => 2
            ],
            [
                'name' => 'พนักงานจัดส่ง ภูเก็ต',
                'email' => 'staff2.phuket@shipcentral.com',
                'password' => 'staff123',
                'branch_index' => 2
            ]
        ];

        foreach ($staffData as $userData) {
            if (isset($branches[$userData['branch_index']])) {
                $branch = $branches[$userData['branch_index']];
                
                $user = User::firstOrCreate(
                    ['email' => $userData['email']],
                    [
                        'name' => $userData['name'],
                        'password' => Hash::make($userData['password']),
                        'user_type' => User::USER_TYPE_BRANCH_STAFF,
                        'branch_id' => $branch->id,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );

                // Assign branch_staff role
                $user->assignRole('branch_staff');
                
                $this->command->info("✅ Created Branch Staff: {$user->name} for {$branch->name}");
            }
        }
    }

    /**
     * Assign permissions to roles based on hierarchy
     */
    private function assignPermissionsToRoles()
    {
        $this->command->info('🔐 Assigning permissions to roles...');

        // Company Admin - Gets ALL permissions
        $companyAdminRole = Role::findByName('company_admin', 'web');
        $allPermissions = Permission::all();
        $companyAdminRole->syncPermissions($allPermissions);

        // Branch Admin - Gets branch management permissions
        $branchAdminRole = Role::findByName('branch_admin', 'web');
        $branchAdminPermissions = [
            // User management within branch
            'admin.user.staff.index', 'admin.user.staff.create', 'admin.user.staff.edit', 'admin.user.staff.delete',
            
            // Own branch management
            'admin.branch.own.index', 'admin.branch.own.edit', 'admin.branch.settings.edit',
            
            // Customer management
            'admin.customer.index', 'admin.customer.create', 'admin.customer.edit', 'admin.customer.delete',
            
            // Shipment operations
            'admin.shipment.create', 'admin.shipment.index', 'admin.shipment.edit', 'admin.shipment.track',
            'admin.shipment.branch.index',
            
            // Carrier credentials
            'admin.carrier.credentials.index', 'admin.carrier.credentials.edit',
            
            // Products
            'admin.product.index', 'admin.product.create', 'admin.product.edit',
            
            // Own branch reports
            'admin.reports.branch.own', 'admin.billing.branch.own',
            
            // Branch audit
            'admin.audit.branch.view'
        ];
        $branchAdminRole->syncPermissions($branchAdminPermissions);

        // Branch Staff - Gets basic operational permissions
        $branchStaffRole = Role::findByName('branch_staff', 'web');
        $branchStaffPermissions = [
            // Basic customer access
            'admin.customer.index',
            
            // Shipment operations
            'admin.shipment.create', 'admin.shipment.index', 'admin.shipment.track',
            'admin.shipment.own.index',
            
            // Products viewing
            'admin.product.index'
        ];
        $branchStaffRole->syncPermissions($branchStaffPermissions);
    }

    /**
     * Display setup summary
     */
    private function displaySummary()
    {
        $this->command->info('');
        $this->command->info('📊 USER SETUP SUMMARY:');
        $this->command->info('===================');
        
        $companyAdmins = User::where('user_type', User::USER_TYPE_COMPANY_ADMIN)->count();
        $branchAdmins = User::where('user_type', User::USER_TYPE_BRANCH_ADMIN)->count();
        $branchStaff = User::where('user_type', User::USER_TYPE_BRANCH_STAFF)->count();
        
        $this->command->info("👑 Company Admins: {$companyAdmins}");
        $this->command->info("🏪 Branch Admins: {$branchAdmins}");
        $this->command->info("👥 Branch Staff: {$branchStaff}");
        $this->command->info("📋 Total Roles: " . Role::count());
        $this->command->info("🔐 Total Permissions: " . Permission::count());
        
        $this->command->info('');
        $this->command->info('🔑 LOGIN CREDENTIALS:');
        $this->command->info('==================');
        $this->command->info('Company Admin: admin@shipcentral.com / admin123');
        $this->command->info('CEO: ceo@shipcentral.com / ceo123');
        $this->command->info('Branch Managers: manager.{branch}@shipcentral.com / manager123');
        $this->command->info('Branch Staff: staff{1-2}.{branch}@shipcentral.com / staff123');
    }
} 