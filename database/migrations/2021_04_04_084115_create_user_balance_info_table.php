<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBalanceInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_balance_info', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('user_account_id');
            //$table->foreign('user_account_id')->references('id')->on('user_accounts');
            $table->uuid('currency_id')->nullable();
            //$table->foreign('currency_id')->references('id')->on('currencies');
            $table->decimal('available_balance', $precision = 10, $scale = 2);
            $table->uuid('user_created')->nullable();
            $table->uuid('user_updated')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        // Schema::create('user_balance_info', function (Blueprint $table) {
        //     $table->uuid('id')->nullable();
        //     $table->uuid('user_account_id');
        //     $table->foreign('user_account_id')->references('id')->on('user_accounts');
        //     $table->uuid('currency_id');
        //     $table->foreign('currency_id')->references('id')->on('currencies');
        //     $table->decimal('available_balance', $precision = 19, $scale = 6);
        //     $table->uuid('user_created')->nullable();
        //     $table->uuid('user_updated')->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_balance_info');
    }
}
