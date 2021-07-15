<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Enums\Addresses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $data = json_decode(Addresses::regions);
        $regions = collect($data)->map(function($value) {
            return [
                'id' => (string)Str::uuid(),
                'name' => $value->name,
                'region_code' => $value->region_code,
            ];
        })->toArray();
        DB::table('regions')->truncate();
        DB::table('regions')->insert($regions);
    }
}
