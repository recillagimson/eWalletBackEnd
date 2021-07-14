<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Enums\Addresses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Province;

class ProvincesSeeder extends Seeder
{
    public $province;

    public function __construct(Province $province)
    {
        $this->province = $province;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('provinces')->truncate();
        $data = json_decode(Addresses::provinces);
        collect($data)->map(function($value) {
            return [
                'id' => (string)Str::uuid(),
                'name' => $value->name,
                'region_code' => $value->region_code,
                'province_code' => $value->province_code,
            ];
        })->chunk(20)->each(function ($chunk)  {
            $this->province::query()->insert($chunk->toArray());
        });
    }
}
