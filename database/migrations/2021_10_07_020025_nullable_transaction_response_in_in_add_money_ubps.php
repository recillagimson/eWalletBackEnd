<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NullableTransactionResponseInInAddMoneyUbps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_add_money_ubps', function (Blueprint $table) {
            $table->json('transaction_response')->nullable()->change();
            $table->uuid('user_updated')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_add_money_ubps', function (Blueprint $table) {
            $table->json('transaction_response')->change();
            $table->uuid('user_updated')->change();
        });
    }
}
