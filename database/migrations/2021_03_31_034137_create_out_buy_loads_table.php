<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutBuyLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('out_buy_loads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_account_id');
            $table->foreign('user_account_id')->references('id')->on('user_accounts');
            $table->uuid('prepaid_load_id');
            $table->foreign('prepaid_load_id')->references('id')->on('prepaid_loads');
            $table->decimal('total_amount', 10, 3);
            $table->timestamp('transaction_date')->nullable();
            $table->uuid('transaction_category_id');
            $table->foreign('transaction_category_id')->references('id')->on('transaction_categories');
            $table->string('transaction_remarks');
            $table->uuid('user_created')->nullable();
            $table->uuid('user_updated')->nullable();
            $table->timestamps();
            $table->timestamp('expires_at')->nullable();
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
        Schema::dropIfExists('out_buy_loads');
    }
}
