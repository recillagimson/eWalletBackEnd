<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDragonpayReferenceNullableInInAddMoneyWebBank extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_add_money_web_banks', function (Blueprint $table) {
            $table->string('dragonpay_reference', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_add_money_web_banks', function (Blueprint $table) {
            $table->string('dragonpay_reference', 50)->change();
        });
    }
}
