<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransactionResponseInInAddMoneyEcPaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_add_money_ec_pays', function (Blueprint $table) {
            $table->string('transaction_response', 350)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_add_money_ec_pays', function (Blueprint $table) {
            $table->dropColumn('transaction_response');
        });
    }
}
