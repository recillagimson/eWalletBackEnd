<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameDragonpayBogReferenceNumberInInAddMoneyFromBank extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_add_money_from_bank', function (Blueprint $table) {
            $table->renameColumn('dragonpay_bog_reference_number', 'dragonpay_channel_reference_number');
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
            $table->renameColumn('dragonpay_channel_reference_number', 'dragonpay_bog_reference_number');
        });
    }
}
