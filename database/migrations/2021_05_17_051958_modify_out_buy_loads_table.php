<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyOutBuyLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('out_buy_loads', function (Blueprint $table) {
            $table->string('reference_number', 50)->change();
            $table->string('provider', 20)->change();
            $table->string('provider_transaction_id', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('out_buy_loads', function (Blueprint $table) {
            $table->string('reference_number');
            $table->dropColumn('provider');
            $table->dropColumn('provider_transaction_id');
        });
    }
}
