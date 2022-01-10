<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutPayMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('out_pay_merchants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_account_id');
            $table->string('merchant_account_number', 50);
            $table->string('reference_number', 50);
            $table->decimal('amount');
            $table->uuid('service_fee_id')->nullable();
            $table->decimal('service_fee');
            $table->decimal('total_amount');
            $table->dateTime('transaction_date');
            $table->uuid('transaction_category_id');
            $table->string('description');
            $table->string('status', 20);
            $table->string('remarks');
            $table->uuid('user_created');
            $table->uuid('user_updated')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('out_pay_merchants');
    }
}
