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
            $table->uuid('receiver_id');
            $table->string('reference_number', 50)->unique();
            $table->decimal('amount', 10, 3);
            $table->decimal('service_fee', 10, 3);
            $table->uuid('service_fee_id');
            $table->decimal('total_amount', 10, 3);
            $table->uuid('purpose_of_transfer_id');
            $table->string('message', 50);
            $table->boolean('status');
            $table->datetime('transaction_date');
            $table->uuid('transction_category_id');
            $table->string('transaction_remarks', 100);
            $table->uuid('user_created');
            $table->uuid('uses_updated');
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
