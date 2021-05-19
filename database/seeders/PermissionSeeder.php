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
        $routes = Route::getRoutes()->getRoutesByName();
        PermissionGroup::truncate();
        Permission::truncate();
        foreach($routes as $key => $route){
            // array_push($routes_keys, $key);
            $terms = explode('.', $key);
            $name = str_replace('.', ' ', str_replace('_', ' ', $terms['0']));
            $name = ucfirst($name);
            $permission_group = PermissionGroup::updateOrCreate([
                'name' => $name . " Management",
                'description' => $name . " Management",
                'slug' => Str::slug($name, '-')
            ]);

            $permission_key_name = str_replace('.', ' ', $key);
            $permission_key_name = str_replace('_', ' ', $permission_key_name);
            Permission::create([
                'name' => $permission_key_name,
                'slug' => Str::slug($permission_key_name, '-'),
                'permission_group_id' => $permission_group->id,
                'route_name' => $key
            ]);
        }
    }
}
