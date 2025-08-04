<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateMenuSeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'core:generate-menu-seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate admin menu seeder data from current database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Generating admin menu seed data from current database...');

        $menus = DB::table('admin_menus')->orderBy('id')->get();
        
        if ($menus->isEmpty()) {
            $this->error('No admin menu data found in database.');
            return 1;
        }

        $this->info('Found ' . $menus->count() . ' menu items.');
        
        // Generate PHP array code
        $output = "[\n";
        
        foreach ($menus as $menu) {
            $output .= "    [\n";
            $output .= "        'id' => {$menu->id},\n";
            $output .= "        'icon' => '{$menu->icon}',\n";
            $output .= "        'name' => '" . addslashes($menu->name) . "',\n";
            $output .= "        'link_type' => {$menu->link_type},\n";
            $output .= "        'url' => " . ($menu->url ? "'" . addslashes($menu->url) . "'" : 'null') . ",\n";
            $output .= "        'route_name' => " . ($menu->route_name ? "'" . addslashes($menu->route_name) . "'" : 'null') . ",\n";
            $output .= "        'target' => '{$menu->target}',\n";
            $output .= "        'sequence' => {$menu->sequence},\n";
            $output .= "        '_lft' => {$menu->_lft},\n";
            $output .= "        '_rgt' => {$menu->_rgt},\n";
            $output .= "        'parent_id' => " . ($menu->parent_id ? $menu->parent_id : 'null') . ",\n";
            $output .= "        'status' => {$menu->status},\n";
            $output .= "        'created_at' => '{$menu->created_at}',\n";
            $output .= "        'updated_at' => '{$menu->updated_at}'\n";
            $output .= "    ],\n";
        }
        
        $output .= "];\n";

        // Save to file
        $filePath = base_path('Modules/Core/Database/Seeders/admin_menu_data.php');
        file_put_contents($filePath, "<?php\n\nreturn " . $output);

        $this->info('Admin menu seed data generated successfully!');
        $this->info('Data saved to: ' . $filePath);
        $this->info('You can now copy this data to your AdminMenuSeeder.php file.');

        // Also output to console
        $this->line("\n" . str_repeat('=', 80));
        $this->line('ADMIN MENU SEED DATA:');
        $this->line(str_repeat('=', 80));
        $this->line($output);
        $this->line(str_repeat('=', 80));

        return 0;
    }
} 