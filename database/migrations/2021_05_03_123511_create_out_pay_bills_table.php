<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutPayBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('out_pay_bills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_account_id');
            $table->foreign('user_account_id')->references('id')->on('user_accounts');
            $table->string('account_number', 50);
            $table->string('reference_number', 50)->unique();
            $table->decimal('amount', 19, 6);
            $table->decimal('service_fee', 19, 6);
            // $table->uuid('service_fee_id');
            // $table->foreign('user_account_id')->references('id')->on('user_accounts');
            $table->decimal('total_amount', 19, 6);
            $table->datetime('transaction_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->uuid('transction_category_id');
            $table->foreign('transction_category_id')->references('id')->on('transaction_categories');
            $table->string('transaction_remarks', 50)->nullable();
            $table->string('email_or_mobile', 50)->nullable();
            $table->string('message', 50)->nullable();
            $table->boolean('status');
            $table->string('billers_code', 50);
            $table->string('billers_name', 50);
            $table->string('bayad_reference_number', 50);
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
        Schema::dropIfExists('out_pay_bills');
    }
}
