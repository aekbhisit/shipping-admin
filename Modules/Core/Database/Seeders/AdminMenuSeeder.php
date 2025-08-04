<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\AdminMenus;

class AdminMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // Check if we should read from database or use static data
        if ($this->shouldReadFromDatabase()) {
            $this->seedFromDatabase();
        } else {
            $this->seedFromStaticData();
        }

        echo "Admin menu seeder completed successfully!\n";
    }

    /**
     * Check if we should read from current database
     */
    private function shouldReadFromDatabase(): bool
    {
        // Check if admin_menus table exists and has data
        try {
            return DB::table('admin_menus')->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Seed from current database data
     */
    private function seedFromDatabase()
    {
        echo "Reading admin menu data from current database...\n";
        
        // Get current data from database
        $currentMenus = DB::table('admin_menus')->orderBy('id')->get();
        
        if ($currentMenus->isEmpty()) {
            echo "No menu data found in database, using static data instead.\n";
            $this->seedFromStaticData();
            return;
        }

        // Clear existing data
        AdminMenus::truncate();

        // Convert to array and insert
        $adminMenus = [];
        foreach ($currentMenus as $menu) {
            $adminMenus[] = [
                'id' => $menu->id,
                'icon' => $menu->icon,
                'name' => $menu->name,
                'link_type' => $menu->link_type,
                'url' => $menu->url,
                'route_name' => $menu->route_name,
                'target' => $menu->target,
                'sequence' => $menu->sequence,
                '_lft' => $menu->_lft,
                '_rgt' => $menu->_rgt,
                'parent_id' => $menu->parent_id,
                'status' => $menu->status,
                'created_at' => $menu->created_at,
                'updated_at' => $menu->updated_at
            ];
        }

        // Insert data
        DB::table('admin_menus')->insert($adminMenus);
        echo "Seeded " . count($adminMenus) . " menu items from database.\n";
    }

    /**
     * Seed from static data array
     */
    private function seedFromStaticData()
    {
        echo "Using static menu data...\n";
        
        // Clear existing data
        AdminMenus::truncate();

        // Static menu data from your existing structure
        $adminMenus = [
            [
                'id' => 13,
                'icon' => 'bx bx-money',
                'name' => 'ธนาคาร',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.crm.bank.index',
                'target' => '_self',
                'sequence' => 24,
                '_lft' => 46,
                '_rgt' => 47,
                'parent_id' => 14,
                'status' => 1,
                'created_at' => '2022-11-19 10:33:03',
                'updated_at' => '2022-11-20 20:18:43'
            ],
            [
                'id' => 14,
                'icon' => 'bx bx-cog',
                'name' => 'ตั้งค่า CRM',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 23,
                '_lft' => 45,
                '_rgt' => 52,
                'parent_id' => null,
                'status' => 1,
                'created_at' => '2022-11-19 10:34:28',
                'updated_at' => '2022-11-19 13:35:42'
            ],
            [
                'id' => 15,
                'icon' => 'bx bx-store',
                'name' => 'ตั้งค่า หุ้นส่วน',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 18,
                '_lft' => 35,
                '_rgt' => 44,
                'parent_id' => null,
                'status' => 1,
                'created_at' => '2022-11-19 10:36:09',
                'updated_at' => '2022-11-19 13:35:41'
            ],
            [
                'id' => 16,
                'icon' => 'bx bx-basketball',
                'name' => 'ประเภทเกมส์',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.crm.game_type.index',
                'target' => '_self',
                'sequence' => 25,
                '_lft' => 48,
                '_rgt' => 49,
                'parent_id' => 14,
                'status' => 1,
                'created_at' => '2022-11-19 10:37:45',
                'updated_at' => '2022-11-21 00:15:51'
            ],
            [
                'id' => 17,
                'icon' => 'bx bx-money',
                'name' => 'ธนาคาร',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 19,
                '_lft' => 36,
                '_rgt' => 37,
                'parent_id' => 15,
                'status' => 1,
                'created_at' => '2022-11-19 10:38:28',
                'updated_at' => '2022-11-19 13:35:41'
            ],
            [
                'id' => 18,
                'icon' => 'bx bx-credit-card',
                'name' => 'ประเภทธนาคาร',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.crm.bank_type.index',
                'target' => '_self',
                'sequence' => 26,
                '_lft' => 50,
                '_rgt' => 51,
                'parent_id' => 14,
                'status' => 1,
                'created_at' => '2022-11-19 10:39:16',
                'updated_at' => '2022-11-21 00:30:58'
            ],
            [
                'id' => 19,
                'icon' => 'bx bx-clipboard',
                'name' => 'กระดาน',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 20,
                '_lft' => 38,
                '_rgt' => 39,
                'parent_id' => 15,
                'status' => 1,
                'created_at' => '2022-11-19 10:40:37',
                'updated_at' => '2022-11-19 13:35:41'
            ],
            [
                'id' => 20,
                'icon' => 'bx bx-cog',
                'name' => 'ตั้งค่า',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 22,
                '_lft' => 42,
                '_rgt' => 43,
                'parent_id' => 15,
                'status' => 1,
                'created_at' => '2022-11-19 10:41:21',
                'updated_at' => '2022-11-19 13:35:42'
            ],
            [
                'id' => 21,
                'icon' => 'bx bx-purchase-tag',
                'name' => 'โปรโมชั่น',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 21,
                '_lft' => 40,
                '_rgt' => 41,
                'parent_id' => 15,
                'status' => 1,
                'created_at' => '2022-11-19 10:42:51',
                'updated_at' => '2022-11-19 13:35:42'
            ],
            [
                'id' => 22,
                'icon' => 'bx bx-money',
                'name' => 'Statement',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 9,
                '_lft' => 17,
                '_rgt' => 26,
                'parent_id' => null,
                'status' => 1,
                'created_at' => '2022-11-19 10:45:26',
                'updated_at' => '2022-11-19 13:35:40'
            ],
            [
                'id' => 23,
                'icon' => 'bx bx-list-check',
                'name' => 'statement',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.statement.list.index',
                'target' => '_self',
                'sequence' => 10,
                '_lft' => 18,
                '_rgt' => 19,
                'parent_id' => 22,
                'status' => 1,
                'created_at' => '2022-11-19 10:46:07',
                'updated_at' => '2022-11-24 23:11:25'
            ],
            [
                'id' => 24,
                'icon' => 'bx bx-message-detail',
                'name' => 'SMS',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.statement.sms.index',
                'target' => '_self',
                'sequence' => 11,
                '_lft' => 20,
                '_rgt' => 21,
                'parent_id' => 22,
                'status' => 1,
                'created_at' => '2022-11-19 10:46:35',
                'updated_at' => '2022-11-23 23:52:49'
            ],
            [
                'id' => 25,
                'icon' => 'bx bx-transfer',
                'name' => 'รายการโอนเงิน',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.statement.transfer.index',
                'target' => '_self',
                'sequence' => 12,
                '_lft' => 22,
                '_rgt' => 23,
                'parent_id' => 22,
                'status' => 1,
                'created_at' => '2022-11-19 10:48:24',
                'updated_at' => '2022-11-23 23:53:28'
            ],
            [
                'id' => 26,
                'icon' => 'bx bx-align-middle',
                'name' => 'temp statement',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.statement.temp.index',
                'target' => '_self',
                'sequence' => 13,
                '_lft' => 24,
                '_rgt' => 25,
                'parent_id' => 22,
                'status' => 1,
                'created_at' => '2022-11-19 10:49:36',
                'updated_at' => '2022-11-23 23:53:55'
            ],
            [
                'id' => 27,
                'icon' => 'bx bx-user',
                'name' => 'ลูกค้า',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 5,
                '_lft' => 9,
                '_rgt' => 16,
                'parent_id' => null,
                'status' => 1,
                'created_at' => '2022-11-19 10:52:40',
                'updated_at' => '2022-11-19 11:17:25'
            ],
            [
                'id' => 28,
                'icon' => 'bx bx-task',
                'name' => 'ใบงาน',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 1,
                '_lft' => 1,
                '_rgt' => 8,
                'parent_id' => null,
                'status' => 1,
                'created_at' => '2022-11-19 10:53:31',
                'updated_at' => '2022-11-19 11:16:52'
            ],
            [
                'id' => 29,
                'icon' => 'bx bx-info-circle',
                'name' => 'ใบงานใหม่',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 2,
                '_lft' => 2,
                '_rgt' => 3,
                'parent_id' => 28,
                'status' => 1,
                'created_at' => '2022-11-19 10:57:03',
                'updated_at' => '2022-11-19 11:16:52'
            ],
            [
                'id' => 30,
                'icon' => 'bx bx-time',
                'name' => 'ใบงานกำลังทำ',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 3,
                '_lft' => 4,
                '_rgt' => 5,
                'parent_id' => 28,
                'status' => 1,
                'created_at' => '2022-11-19 10:58:21',
                'updated_at' => '2022-11-19 11:16:52'
            ],
            [
                'id' => 31,
                'icon' => 'bx bx-select-multiple',
                'name' => 'ใบงานเสร็จ',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 4,
                '_lft' => 6,
                '_rgt' => 7,
                'parent_id' => 28,
                'status' => 1,
                'created_at' => '2022-11-19 10:59:24',
                'updated_at' => '2022-11-19 11:16:52'
            ],
            [
                'id' => 32,
                'icon' => 'bx bx-user-circle',
                'name' => 'รายการลูกค้า',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.customer.customer.index',
                'target' => '_self',
                'sequence' => 6,
                '_lft' => 10,
                '_rgt' => 11,
                'parent_id' => 27,
                'status' => 1,
                'created_at' => '2022-11-19 11:08:23',
                'updated_at' => '2022-11-19 22:59:25'
            ],
            [
                'id' => 33,
                'icon' => 'bx bx-purchase-tag',
                'name' => 'รับโปรโมชั่น',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.customer.temppromotion.index',
                'target' => '_self',
                'sequence' => 7,
                '_lft' => 12,
                '_rgt' => 13,
                'parent_id' => 27,
                'status' => 1,
                'created_at' => '2022-11-19 11:09:27',
                'updated_at' => '2022-11-19 11:17:25'
            ],
            [
                'id' => 34,
                'icon' => 'bx bx-bar-chart-alt',
                'name' => 'รายงาน',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 14,
                '_lft' => 27,
                '_rgt' => 34,
                'parent_id' => null,
                'status' => 1,
                'created_at' => '2022-11-19 11:12:27',
                'updated_at' => '2022-11-19 13:35:41'
            ],
            [
                'id' => 35,
                'icon' => 'bx bx-user-circle',
                'name' => 'ลูกค้า',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 15,
                '_lft' => 28,
                '_rgt' => 29,
                'parent_id' => 34,
                'status' => 1,
                'created_at' => '2022-11-19 11:13:14',
                'updated_at' => '2022-11-19 13:35:41'
            ],
            [
                'id' => 36,
                'icon' => 'bx bx-dollar-circle',
                'name' => 'รายได้',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 16,
                '_lft' => 30,
                '_rgt' => 31,
                'parent_id' => 34,
                'status' => 1,
                'created_at' => '2022-11-19 11:13:52',
                'updated_at' => '2022-11-19 13:35:41'
            ],
            [
                'id' => 37,
                'icon' => 'bx bx-task',
                'name' => 'ใบงาน',
                'link_type' => 1,
                'url' => null,
                'route_name' => null,
                'target' => '_self',
                'sequence' => 17,
                '_lft' => 32,
                '_rgt' => 33,
                'parent_id' => 34,
                'status' => 1,
                'created_at' => '2022-11-19 11:15:36',
                'updated_at' => '2022-11-19 13:35:41'
            ],
            [
                'id' => 38,
                'icon' => 'bx bx-mobile',
                'name' => 'OTP',
                'link_type' => 1,
                'url' => null,
                'route_name' => 'admin.customer.otp.index',
                'target' => '_self',
                'sequence' => 8,
                '_lft' => 14,
                '_rgt' => 15,
                'parent_id' => 27,
                'status' => 1,
                'created_at' => '2022-11-19 13:34:22',
                'updated_at' => '2022-11-19 13:34:22'
            ]
        ];

        // Insert data using DB::table for bulk insert
        DB::table('admin_menus')->insert($adminMenus);
        echo "Seeded " . count($adminMenus) . " static menu items.\n";
    }

    /**
     * Generate seeder data from current database
     * Run this method to export current admin_menus data as PHP array
     */
    public function exportCurrentData()
    {
        $menus = DB::table('admin_menus')->get();
        
        echo "Current admin_menus data:\n";
        echo "[\n";
        
        foreach ($menus as $menu) {
            echo "    [\n";
            echo "        'id' => {$menu->id},\n";
            echo "        'icon' => '{$menu->icon}',\n";
            echo "        'name' => '{$menu->name}',\n";
            echo "        'link_type' => {$menu->link_type},\n";
            echo "        'url' => " . ($menu->url ? "'{$menu->url}'" : 'null') . ",\n";
            echo "        'route_name' => " . ($menu->route_name ? "'{$menu->route_name}'" : 'null') . ",\n";
            echo "        'target' => '{$menu->target}',\n";
            echo "        'sequence' => {$menu->sequence},\n";
            echo "        '_lft' => {$menu->_lft},\n";
            echo "        '_rgt' => {$menu->_rgt},\n";
            echo "        'parent_id' => " . ($menu->parent_id ? $menu->parent_id : 'null') . ",\n";
            echo "        'status' => {$menu->status},\n";
            echo "        'created_at' => '{$menu->created_at}',\n";
            echo "        'updated_at' => '{$menu->updated_at}'\n";
            echo "    ],\n";
        }
        
        echo "];\n";
    }
} 