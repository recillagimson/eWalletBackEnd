<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTransactionRemarksLenghtInInAddMoneyWebBanks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_add_money_web_banks', function (Blueprint $table) {
            $table->string('transaction_remarks', 70)->change();
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
            $table->string('transaction_remarks', 50)->change();
        });
    }
}
