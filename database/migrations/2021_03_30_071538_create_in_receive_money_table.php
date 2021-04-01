<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInReceiveMoneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_receive_money', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('user_account_id');
            $table->foreign('user_account_id')->references('id')->on('user_accounts');
            $table->uuid('sender_id');
            $table->foreign('sender_id')->references('id')->on('user_accounts');
            $table->string('reference_number', 50)->nullable();
            $table->decimal('amount', 10, 3);
            $table->boolean('status')->nullable();
            $table->datetime('transaction_date');
            // $table->uuid('transction_category_id');
            // $table->foreign('transction_category_id')->references('id')->on('user_accounts');
            $table->string('transaction_remarks', 100);
            $table->uuid('user_created')->nullable();
            $table->uuid('user_updated')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
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
        Schema::dropIfExists('in_receive_money');
        
    }
}
