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
            $table->uuid('id')->primary();
            $table->uuid('user_account_id')->references('id')->on('user_accounts')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->uuid('currency_id')->references('id')->on('currencies')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->double('available_balance');
            $table->double('pending_balance');
            $table->uuid('user_created')->references('id')->on('user_accounts')
                ->onDelete('restrict');
            $table->uuid('user_updated')->references('id')->on('user_accounts')
                ->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
        });
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
