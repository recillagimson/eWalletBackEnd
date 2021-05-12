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
            $table->uuid('user_account_id')->references('id')->on('user_accounts')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->uuid('transaction_id');
            $table->string('reference_number');
            $table->uuid('transaction_category_id')->references('id')->on('transaction_categories')
                ->onDelete('restrict');
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
        Schema::dropIfExists('user_transaction_histories');
    }
}
