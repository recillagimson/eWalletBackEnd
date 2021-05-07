<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropOutBuyLoadsPrepaidLoadIdForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('out_buy_loads', function (Blueprint $table) {
    
            $table->dropForeign('out_buy_loads_prepaid_load_id_foreign'); // Drops index 'geo_state_index'
            $table->dropColumn('prepaid_load_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
} 