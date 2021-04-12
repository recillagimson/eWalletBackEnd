<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTransactionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_transaction_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_account_id');
            $table->uuid('transaction_id');
            $table->string('reference_number', 50);
            $table->uuid('transaction_category_id');
            $table->uuid('user_created');
            $table->uuid('user_updated')->nullable();
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
        Schema::dropIfExists('user_transaction_histories');
    }
}
