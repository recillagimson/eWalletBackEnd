<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\Admin\Permission;
use App\Models\Admin\PermissionGroup;
use Illuminate\Support\Facades\Route;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $routes = Route::getRoutes();
        PermissionGroup::truncate();
        Permission::truncate();
        foreach($routes as $key => $route){
            // dd($route->getActionMethod());
            // dd($route->getActionName());
            // // array_push($routes_keys, $key);
            // $terms = explode('.', $key);
            // $name = str_replace('.', ' ', str_replace('_', ' ', $terms['0']));
            // $name = ucfirst($name);

            $route_raw = $route->getActionName();
            $data = explode("\\", $route_raw);
            $controller = explode("@", $data[count($data) - 1]);
            $module = str_replace('Controller', '', $controller['0']);
            // dd($controller);s

            $permission_group = PermissionGroup::updateOrCreate([
                'name' => $module . " Management",
                'description' => $module . " Management",
                'slug' => Str::slug($module, '-')
            ]);

            // $permission_key_name = str_replace('.', ' ', $key);
            // $permission_key_name = str_replace('_', ' ', $permission_key_name);
                // if(!isset($controller['1'])){
                //     dd($controller);
                // }

            if(isset($controller['1'])) {
                Permission::create([
                    'name' => $module . "-" . $controller['1'],
                    'slug' => Str::slug($module . "-" . $controller['1'], '-'),
                    'permission_group_id' => $permission_group->id,
                    'route_name' => $route_raw
                ]);
            }
        }
    }
}
