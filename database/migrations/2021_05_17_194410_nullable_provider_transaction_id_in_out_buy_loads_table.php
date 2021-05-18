<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NullableProviderTransactionIdInOutBuyLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('out_buy_loads', function (Blueprint $table) {
            $table->string('provider_transaction_id', 50)->nullable()->change();
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
            $table->string('provider_transaction_id', 50)->change();
        });
    }
}
