<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutWithdraws extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('out_withdraws', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_account_id');
            $table->string('reference_number', 50);
            $table->string('account_name', 50);
            $table->string('account_number', 50);
            $table->string('sender_recepient_to', 50)->nullable();
            $table->string('purpose', 50)->nullable();
            $table->decimal('amount', 10, 3);
            $table->decimal('service_fee', 10, 3);
            $table->uuid('service_fee_id');
            $table->decimal('total_amount', 10, 3);
            $table->datetime('transaction_date');
            $table->uuid('transction_category_id');
            $table->string('transaction_remarks', 100);
            $table->string('status', 10);
            $table->string('pesonet_reference', 50)->nullable();
            $table->string('instapay_reference', 50)->nullable();
            $table->uuid('user_created');
            $table->uuid('user_updated')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->unique(array('reference_number', 'account_number'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('out_withdraws');
    }
}
