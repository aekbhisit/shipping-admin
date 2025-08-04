<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ShipCentralMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Purpose: Create comprehensive admin menu structure for ShipCentral
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->command->info('🚀 Setting up ShipCentral Admin Menu Structure...');

        // Clear existing menu data
        DB::table('admin_menus')->truncate();

        // Create the complete menu structure
        $this->createMenuStructure();

        $this->command->info('✅ ShipCentral Admin Menu setup completed successfully!');
    }

    /**
     * Create the complete menu structure for ShipCentral
     */
    private function createMenuStructure()
    {
        $menuStructure = [
            // Dashboard - Available to all users
            [
                'id' => 1,
                'icon' => 'bx bx-home-alt',
                'name' => 'Dashboard',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.dashboard.index',
                'target' => '_self',
                'sequence' => 1,
                '_lft' => 1,
                '_rgt' => 2,
                'parent_id' => null,
                'status' => 1,
            ],

            // User Management - Company Admin & Branch Admin
            [
                'id' => 10,
                'icon' => 'bx bx-user',
                'name' => 'จัดการผู้ใช้งาน',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.user.user.index',
                'target' => '_self',
                'sequence' => 2,
                '_lft' => 3,
                '_rgt' => 14,
                'parent_id' => null,
                'status' => 1,
            ],
            [
                'id' => 11,
                'icon' => 'bx bx-crown',
                'name' => 'ผู้ดูแลระบบ',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.user.company.index',
                'target' => '_self',
                'sequence' => 1,
                '_lft' => 4,
                '_rgt' => 5,
                'parent_id' => 10,
                'status' => 1,
            ],
            [
                'id' => 12,
                'icon' => 'bx bx-user-check',
                'name' => 'ผู้จัดการสาขา',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.user.branch.index',
                'target' => '_self',
                'sequence' => 2,
                '_lft' => 6,
                '_rgt' => 7,
                'parent_id' => 10,
                'status' => 1,
            ],
            [
                'id' => 13,
                'icon' => 'bx bx-user',
                'name' => 'พนักงานสาขา',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.user.staff.index',
                'target' => '_self',
                'sequence' => 3,
                '_lft' => 8,
                '_rgt' => 9,
                'parent_id' => 10,
                'status' => 1,
            ],

            // Branch Management - Company Admin & Branch Admin
            [
                'id' => 20,
                'icon' => 'bx bx-store',
                'name' => 'จัดการสาขา',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 3,
                '_lft' => 15,
                '_rgt' => 24,
                'parent_id' => null,
                'status' => 1,
            ],
            [
                'id' => 21,
                'icon' => 'bx bx-buildings',
                'name' => 'ทุกสาขา',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.branch.all.index',
                'target' => '_self',
                'sequence' => 1,
                '_lft' => 16,
                '_rgt' => 17,
                'parent_id' => 20,
                'status' => 1,
            ],
            [
                'id' => 22,
                'icon' => 'bx bx-store-alt',
                'name' => 'สาขาของฉัน',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.branch.own.index',
                'target' => '_self',
                'sequence' => 2,
                '_lft' => 18,
                '_rgt' => 19,
                'parent_id' => 20,
                'status' => 1,
            ],

            // Customer Management - All users
            [
                'id' => 30,
                'icon' => 'bx bx-group',
                'name' => 'จัดการลูกค้า',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.customer.index',
                'target' => '_self',
                'sequence' => 4,
                '_lft' => 25,
                '_rgt' => 26,
                'parent_id' => null,
                'status' => 1,
            ],

            // Shipment Management - All users
            [
                'id' => 40,
                'icon' => 'bx bx-package',
                'name' => 'จัดการการจัดส่ง',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 5,
                '_lft' => 27,
                '_rgt' => 38,
                'parent_id' => null,
                'status' => 1,
            ],
            [
                'id' => 41,
                'icon' => 'bx bx-plus',
                'name' => 'สร้างรายการจัดส่ง',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.shipment.create',
                'target' => '_self',
                'sequence' => 1,
                '_lft' => 28,
                '_rgt' => 29,
                'parent_id' => 40,
                'status' => 1,
            ],
            [
                'id' => 42,
                'icon' => 'bx bx-list-ul',
                'name' => 'รายการทั้งหมด',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.shipment.all.index',
                'target' => '_self',
                'sequence' => 2,
                '_lft' => 30,
                '_rgt' => 31,
                'parent_id' => 40,
                'status' => 1,
            ],
            [
                'id' => 43,
                'icon' => 'bx bx-list-check', 
                'name' => 'รายการสาขา',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.shipment.branch.index',
                'target' => '_self',
                'sequence' => 3,
                '_lft' => 32,
                '_rgt' => 33,
                'parent_id' => 40,
                'status' => 1,
            ],

            // Product Management - All users
            [
                'id' => 50,
                'icon' => 'bx bx-box',
                'name' => 'จัดการสินค้า',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.product.index',
                'target' => '_self',
                'sequence' => 6,
                '_lft' => 39,
                '_rgt' => 40,
                'parent_id' => null,
                'status' => 1,
            ],

            // Carrier Management - Company Admin & Branch Admin
            [
                'id' => 60,
                'icon' => 'bx bx-truck',
                'name' => 'ผู้ให้บริการขนส่ง',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.carrier.index',
                'target' => '_self',
                'sequence' => 7,
                '_lft' => 41,
                '_rgt' => 42,
                'parent_id' => null,
                'status' => 1,
            ],

            // Reports & Analytics - Company Admin & Branch Admin
            [
                'id' => 70,
                'icon' => 'bx bx-bar-chart',
                'name' => 'รายงานและสถิติ',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 8,
                '_lft' => 43,
                '_rgt' => 54,
                'parent_id' => null,
                'status' => 1,
            ],
            [
                'id' => 71,
                'icon' => 'bx bx-tachometer',
                'name' => 'Dashboard ระบบ',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.reports.system.dashboard',
                'target' => '_self',
                'sequence' => 1,
                '_lft' => 44,
                '_rgt' => 45,
                'parent_id' => 70,
                'status' => 1,
            ],
            [
                'id' => 72,
                'icon' => 'bx bx-building',
                'name' => 'รายงานสาขา',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.reports.branches.index',
                'target' => '_self',
                'sequence' => 2,
                '_lft' => 46,
                '_rgt' => 47,
                'parent_id' => 70,
                'status' => 1,
            ],

            // Billing Management - Company Admin & Branch Admin
            [
                'id' => 80,
                'icon' => 'bx bx-money',
                'name' => 'จัดการค่าใช้จ่าย',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 9,
                '_lft' => 55,
                '_rgt' => 64,
                'parent_id' => null,
                'status' => 1,
            ],
            [
                'id' => 81,
                'icon' => 'bx bx-receipt',
                'name' => 'ค่าใช้จ่ายทั้งหมด',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.billing.all.index',
                'target' => '_self',
                'sequence' => 1,
                '_lft' => 56,
                '_rgt' => 57,
                'parent_id' => 80,
                'status' => 1,
            ],
            [
                'id' => 82,
                'icon' => 'bx bx-export',
                'name' => 'ส่งออกข้อมูล',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.billing.export.index',
                'target' => '_self',
                'sequence' => 2,
                '_lft' => 58,
                '_rgt' => 59,
                'parent_id' => 80,
                'status' => 1,
            ],

            // Audit & Compliance - Company Admin only
            [
                'id' => 90,
                'icon' => 'bx bx-shield-check',
                'name' => 'ตรวจสอบและติดตาม',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.audit.index',
                'target' => '_self',
                'sequence' => 10,
                '_lft' => 65,
                '_rgt' => 66,
                'parent_id' => null,
                'status' => 1,
            ],

            // System Settings - Company Admin only
            [
                'id' => 100,
                'icon' => 'bx bx-cog',
                'name' => 'ตั้งค่าระบบ',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 11,
                '_lft' => 67,
                '_rgt' => 76,
                'parent_id' => null,
                'status' => 1,
            ],
            [
                'id' => 101,
                'icon' => 'bx bx-menu',
                'name' => 'เมนูผู้ใช้',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.admin_menu.admin_menu.index',
                'target' => '_self',
                'sequence' => 1,
                '_lft' => 68,
                '_rgt' => 69,
                'parent_id' => 100,
                'status' => 1,
            ],
            [
                'id' => 102,
                'icon' => 'bx bx-globe',
                'name' => 'ตั้งค่าเว็บไซต์',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.setting.web.index',
                'target' => '_self',
                'sequence' => 2,
                '_lft' => 70,
                '_rgt' => 71,
                'parent_id' => 100,
                'status' => 1,
            ],
            [
                'id' => 103,
                'icon' => 'bx bx-folder',
                'name' => 'จัดการไฟล์',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.filemanager.filemanager.index',
                'target' => '_self',
                'sequence' => 3,
                '_lft' => 72,
                '_rgt' => 73,
                'parent_id' => 100,
                'status' => 1,
            ],

            // Favorites/Likes - All users
            [
                'id' => 110,
                'icon' => 'bx bx-heart',
                'name' => 'รายการโปรด',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 12,
                '_lft' => 77,
                '_rgt' => 86,
                'parent_id' => null,
                'status' => 1,
            ],
            [
                'id' => 111,
                'icon' => 'bx bx-package',
                'name' => 'การจัดส่งที่ชอบ',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.favorites.shipments.index',
                'target' => '_self',
                'sequence' => 1,
                '_lft' => 78,
                '_rgt' => 79,
                'parent_id' => 110,
                'status' => 1,
            ],
            [
                'id' => 112,
                'icon' => 'bx bx-group',
                'name' => 'ลูกค้าที่ชอบ',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.favorites.customers.index',
                'target' => '_self',
                'sequence' => 2,
                '_lft' => 80,
                '_rgt' => 81,
                'parent_id' => 110,
                'status' => 1,
            ],
            [
                'id' => 113,
                'icon' => 'bx bx-box',
                'name' => 'สินค้าที่ชอบ',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.favorites.products.index',
                'target' => '_self',
                'sequence' => 3,
                '_lft' => 82,
                '_rgt' => 83,
                'parent_id' => 110,
                'status' => 1,
            ],
            [
                'id' => 114,
                'icon' => 'bx bx-star',
                'name' => 'รายการที่บันทึก',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.favorites.saved.index',
                'target' => '_self',
                'sequence' => 4,
                '_lft' => 84,
                '_rgt' => 85,
                'parent_id' => 110,
                'status' => 1,
            ],
        ];

        // Insert menu items
        foreach ($menuStructure as $menu) {
            DB::table('admin_menus')->insert(array_merge($menu, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        $this->command->info('✅ Created ' . count($menuStructure) . ' menu items');
        $this->command->info('📋 Menu structure with role-based permissions ready!');
    }
} 