<?php

namespace Database\Seeders\Test\Auth;

use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
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
        DB::table('clients')->delete();

        if (App::environment(['local', 'staging'])) {
            Client::create([
                'client_id' => 'spa-client',
                'client_secret' => Hash::make('secret')
            ]);
        } else {
            Client::create([
                'client_id' => 'spa-client',
                'client_secret' => Hash::make('iZMsCTcJGyC1duTWR3=H&IG')
            ]);

            Client::create([
                'client_id' => 'android-client',
                'client_secret' => Hash::make('6ptfW-q&_s#7vKgni3u!NNA')
            ]);

            Client::create([
                'client_id' => 'ios-client',
                'client_secret' => Hash::make('HMGIfmLD=#|_%7hCfrFl^i?')
            ]);
        }
    }
}
