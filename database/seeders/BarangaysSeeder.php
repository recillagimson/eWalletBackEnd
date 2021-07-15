<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Enums\Addresses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Barangay;

class BarangaysSeeder extends Seeder
{
    public $barangay;

    public function __construct(Barangay $barangay)
    {
        $this->barangay = $barangay;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('barangays')->truncate();
        $data = json_decode(Addresses::barangays);
        collect($data)->map(function($value) {
            return [
                'id' => (string)Str::uuid(),
                'name' => $value->name,
                'municipality_code' => $value->municipality_code,
            ];
        })->chunk(500)->each(function ($chunk)  {
            $this->barangay::query()->insert($chunk->toArray());
        });
    }
}
