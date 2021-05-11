<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInOutBuyLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('out_buy_loads', function (Blueprint $table) {
            $table->string('recipient_mobile_number', 15)->nullable();
            $table->string('provider', 10)->nullable();
            $table->string('product_code', 15)->nullable();
            $table->longText('transaction_response')->nullable();
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
            $table->dropColumn('recipient_mobile_number');
            $table->dropColumn('provider');
            $table->dropColumn('product_code');
            $table->dropColumn('transaction_response');
        });
    }
}
