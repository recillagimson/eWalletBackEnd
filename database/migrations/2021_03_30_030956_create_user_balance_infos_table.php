<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBalanceInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_balance_infos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('user_account_id');
            $table->foreign('user_account_id')->references('id')->on('user_accounts');
            // $table->uuid('currency_id');
            // $table->foreign('currency_id')->references('id')->on('currencies');
            $table->decimal('available_balance', $precision = 10, $scale = 3);
            $table->uuid('user_created')->nullable();
            $table->uuid('user_updated')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_balance_infos');
    }
}
