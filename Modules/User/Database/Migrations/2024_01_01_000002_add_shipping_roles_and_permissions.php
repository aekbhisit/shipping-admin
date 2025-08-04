<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddShippingRolesAndPermissions extends Migration
{
    /**
     * Run the migrations.
     * Purpose: Add shipping-specific roles and permissions
     * Enhancement Type: ADD_DATA (enhance existing roles/permissions)
     *
     * @return void
     */
    public function up()
    {
        // Add shipping-specific roles
        $roles = [
            [
                'name' => 'company_admin',
                'display_name' => 'Company Administrator',
                'description' => 'Full system access across all branches',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'branch_admin',
                'display_name' => 'Branch Administrator',
                'description' => 'Manage users and operations within assigned branch',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'branch_staff',
                'display_name' => 'Branch Staff',
                'description' => 'Handle shipping operations within assigned branch',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                $role
            );
        }

        // Add shipping-specific permissions
        $permissions = [
            // User Management Permissions
            [
                'name' => 'users.manage_all',
                'display_name' => 'Manage All Users',
                'description' => 'Create, edit, delete users across all branches',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'users.manage_branch',
                'display_name' => 'Manage Branch Users',
                'description' => 'Manage users within assigned branch only',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'users.view_all',
                'display_name' => 'View All Users',
                'description' => 'View users across all branches',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'users.view_branch',
                'display_name' => 'View Branch Users',
                'description' => 'View users within assigned branch only',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Branch Access Permissions
            [
                'name' => 'branches.access_all',
                'display_name' => 'Access All Branches',
                'description' => 'Access and manage all branch operations',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'branches.access_own',
                'display_name' => 'Access Own Branch',
                'description' => 'Access assigned branch operations only',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'branches.manage',
                'display_name' => 'Manage Branches',
                'description' => 'Create, edit, delete branch configurations',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Shipment Management Permissions
            [
                'name' => 'shipments.create',
                'display_name' => 'Create Shipments',
                'description' => 'Create new shipment records',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'shipments.edit',
                'display_name' => 'Edit Shipments',
                'description' => 'Modify existing shipment records',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'shipments.delete',
                'display_name' => 'Delete Shipments',
                'description' => 'Delete shipment records',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'shipments.view_all',
                'display_name' => 'View All Shipments',
                'description' => 'View shipments across all branches',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'shipments.view_branch',
                'display_name' => 'View Branch Shipments',
                'description' => 'View shipments within assigned branch only',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Customer Management Permissions
            [
                'name' => 'customers.manage',
                'display_name' => 'Manage Customers',
                'description' => 'Create, edit, delete customer records',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'customers.view_all',
                'display_name' => 'View All Customers',
                'description' => 'View customers across all branches',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'customers.view_branch',
                'display_name' => 'View Branch Customers',
                'description' => 'View customers within assigned branch only',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Product Management Permissions
            [
                'name' => 'products.manage',
                'display_name' => 'Manage Products',
                'description' => 'Create, edit, delete product records',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'products.view',
                'display_name' => 'View Products',
                'description' => 'View product catalog',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Reporting Permissions
            [
                'name' => 'reports.view_all',
                'display_name' => 'View All Reports',
                'description' => 'View reports across all branches',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'reports.view_branch',
                'display_name' => 'View Branch Reports',
                'description' => 'View reports for assigned branch only',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'reports.export',
                'display_name' => 'Export Reports',
                'description' => 'Export report data',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Audit Permissions
            [
                'name' => 'audit.view_all',
                'display_name' => 'View All Audit Logs',
                'description' => 'View audit logs across all branches',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'audit.view_branch',
                'display_name' => 'View Branch Audit Logs',
                'description' => 'View audit logs for assigned branch only',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // System Administration Permissions
            [
                'name' => 'system.settings',
                'display_name' => 'System Settings',
                'description' => 'Manage system-wide settings and configurations',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'system.maintenance',
                'display_name' => 'System Maintenance',
                'description' => 'Perform system maintenance tasks',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove shipping-specific roles
        $rolesToRemove = ['company_admin', 'branch_admin', 'branch_staff'];
        DB::table('roles')->whereIn('name', $rolesToRemove)->delete();

        // Remove shipping-specific permissions
        $permissionsToRemove = [
            'users.manage_all', 'users.manage_branch', 'users.view_all', 'users.view_branch',
            'branches.access_all', 'branches.access_own', 'branches.manage',
            'shipments.create', 'shipments.edit', 'shipments.delete', 'shipments.view_all', 'shipments.view_branch',
            'customers.manage', 'customers.view_all', 'customers.view_branch',
            'products.manage', 'products.view',
            'reports.view_all', 'reports.view_branch', 'reports.export',
            'audit.view_all', 'audit.view_branch',
            'system.settings', 'system.maintenance'
        ];
        
        DB::table('permissions')->whereIn('name', $permissionsToRemove)->delete();
    }

    /**
     * Assign permissions to roles based on hierarchy
     */
    private function assignPermissionsToRoles()
    {
        // Company Admin - Full access to everything
        $companyAdminRole = DB::table('roles')->where('name', 'company_admin')->first();
        if ($companyAdminRole) {
            $allPermissions = DB::table('permissions')->pluck('id')->toArray();
            
            foreach ($allPermissions as $permissionId) {
                DB::table('role_has_permissions')->updateOrInsert([
                    'role_id' => $companyAdminRole->id,
                    'permission_id' => $permissionId
                ]);
            }
        }

        // Branch Admin - Branch-scoped management permissions
        $branchAdminRole = DB::table('roles')->where('name', 'branch_admin')->first();
        if ($branchAdminRole) {
            $branchAdminPermissions = [
                'users.manage_branch', 'users.view_branch',
                'branches.access_own',
                'shipments.create', 'shipments.edit', 'shipments.view_branch',
                'customers.manage', 'customers.view_branch',
                'products.view',
                'reports.view_branch', 'reports.export',
                'audit.view_branch'
            ];

            foreach ($branchAdminPermissions as $permissionName) {
                $permission = DB::table('permissions')->where('name', $permissionName)->first();
                if ($permission) {
                    DB::table('role_has_permissions')->updateOrInsert([
                        'role_id' => $branchAdminRole->id,
                        'permission_id' => $permission->id
                    ]);
                }
            }
        }

        // Branch Staff - Basic operational permissions
        $branchStaffRole = DB::table('roles')->where('name', 'branch_staff')->first();
        if ($branchStaffRole) {
            $branchStaffPermissions = [
                'users.view_branch',
                'branches.access_own',
                'shipments.create', 'shipments.edit', 'shipments.view_branch',
                'customers.view_branch',
                'products.view'
            ];

            foreach ($branchStaffPermissions as $permissionName) {
                $permission = DB::table('permissions')->where('name', $permissionName)->first();
                if ($permission) {
                    DB::table('role_has_permissions')->updateOrInsert([
                        'role_id' => $branchStaffRole->id,
                        'permission_id' => $permission->id
                    ]);
                }
            }
        }
    }
} 