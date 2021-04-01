<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutSendMoneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('out_send_money', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_account_id');
            $table->foreign('user_account_id')->references('id')->on('user_accounts');
            $table->uuid('receiver_id');
            $table->foreign('receiver_id')->references('id')->on('user_accounts');
            $table->string('reference_number', 50)->nullable();
            $table->decimal('amount', 10, 3);
            $table->decimal('service_fee', 10, 3);
            // $table->uuid('service_fee_id');
            // $table->foreign('service_fee_id')->references('id')->on('service_fees');
            $table->decimal('total_amount', 10, 3);
            // $table->uuid('purpose_of_transfer_id');
            // $table->foreign('purpose_of_transfer_id')->references('id')->on('purposes_of_transfer');
            $table->string('message', 50);
            $table->boolean('status')->nullable();
            $table->datetime('transaction_date');
            // $table->uuid('transction_category_id');
            // $table->foreign('transction_category_id')->references('id')->on('purposes_of_transfer');
            $table->string('transaction_remarks', 100)->nullable();
            $table->uuid('user_created')->nullable();;
            $table->uuid('user_updated')->nullable();;
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
        Schema::dropIfExists('out_send_money');
    }
}
