<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpiredOnInInAddMoneyFromBank extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_add_money_from_bank', function (Blueprint $table) {
            $table->dateTime('expired_on')->nullable();
            $table->json('transaction_response')->nullable();
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
            $table->dropColumn('expired_on');
            $table->dropColumn('transaction_response');
        });
    }
}
