<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutSendToBanksTable extends Migration
{
    /**
     * Run the migrations.
     * 
     *
     * @return void
     */
    public function up()
    {
        Schema::create('out_send_to_banks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_account_id');
            $table->uuid('online_bank_or_over_the_counter_list_id')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('account_name', 50)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('sender_recepient_to', 50)->nullable();
            $table->string('purpose', 50)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->decimal('service_fee', 10, 2)->nullable();
            $table->uuid('service_fee_id')->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->timestamp('transaction_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->uuid('transaction_category_id')->nullable();
            $table->string('transaction_remarks', 50)->nullable();
            $table->string('status', 20)->nullable();
            $table->string('pesonet_reference', 50)->nullable();
            $table->string('instapay_reference', 50)->nullable();
            $table->string('user_created', 50)->nullable();
            $table->string('user_updated', 50)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('out_send_to_banks');
    }
}
