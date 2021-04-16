<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NullableDragonpayChannelReferenceNumberInInAddMoneyFromBank extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_add_money_from_bank', function (Blueprint $table) {
            $table->string('dragonpay_reference', 50)->nullable()->change();
            $table->string('dragonpay_channel_reference_number', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_add_money_from_bank', function (Blueprint $table) {
            $table->string('dragonpay_reference', 50)->change();
            $table->string('dragonpay_channel_reference_number', 50)->change();
        });
    }
}
