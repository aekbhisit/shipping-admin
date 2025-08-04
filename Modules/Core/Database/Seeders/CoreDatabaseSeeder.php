<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class CoreDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // Call the comprehensive ShipCentral menu seeder
        $this->call(ShipCentralMenuSeeder::class);
        
        // Legacy menu seeder (if needed)
        // $this->call(AdminMenuSeeder::class);
        
        // $this->call("OthersTableSeeder");
    }
}
