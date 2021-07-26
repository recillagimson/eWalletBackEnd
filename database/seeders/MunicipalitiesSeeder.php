<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Enums\Addresses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Municipality;

class MunicipalitiesSeeder extends Seeder
{
    public $municipality;

    public function __construct(Municipality $municipality)
    {
        $this->municipality = $municipality;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('municipalities')->truncate();
        $data = json_decode(Addresses::municipalities);
        collect($data)->map(function($value) {
            return [
                'id' => (string)Str::uuid(),
                'name' => $value->name,
                'municipality_code' => $value->municipality_code,
                'province_code' => $value->province_code,
                'zip_code' => $value->zip_code,
            ];
        })->chunk(500)->each(function ($chunk)  {
            $this->municipality::query()->insert($chunk->toArray());
        });
    }
}
