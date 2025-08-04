<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SettingDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        
        Model::unguard();

        $now = DB::raw('NOW()');
        DB::statement("ALTER TABLE slugs AUTO_INCREMENT=1");
        DB::table('slugs')->truncate();
        DB::table('slugs')->insert(
            [
                [
                    'id' => 1,
                    'type' => 1,
                    'level' => 1,
                    'slug_uid' => 'index-index-1',
                    'slug' => 'index',
                    'lang' => 'en',
                    'module' => 'index',
                    'method' => 'index',
                    'data_id' => 1,
                    'param' => '',
                    'meta_auther' => '',
                    'meta_title' => '',
                    'meta_keywords' => '',
                    'meta_description' => '',
                    'meta_image' => '',
                    'meta_robots' => '',
                    'updated_at' => $now,
                    'created_at' => $now,
                ],
                [
                    'id' => 2,
                    'type' => 1,
                    'level' => 1,
                    'slug_uid' => 'index-index-1',
                    'slug' => 'หน้าแรก',
                    'lang' => 'th',
                    'module' => 'index',
                    'method' => 'index',
                    'data_id' => 1,
                    'param' => '',
                    'meta_auther' => '',
                    'meta_title' => '',
                    'meta_keywords' => '',
                    'meta_description' => '',
                    'meta_image' => '',
                    'meta_robots' => '',
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            ]
        );
    
    }
}
