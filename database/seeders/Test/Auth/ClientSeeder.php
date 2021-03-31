<?php

namespace Database\Seeders\Test\Auth;

use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Client::create([
           'client_id' => 'spa-client',
           'client_secret' => Hash::make('secret')
        ]);
    }
}
