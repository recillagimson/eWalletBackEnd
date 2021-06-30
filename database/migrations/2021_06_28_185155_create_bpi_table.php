<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBpiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_add_money_bpi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_account_id');
            $table->string('reference_number');
            $table->double('amount');
            $table->uuid('service_fee_id');
            $table->double('service_fee');
            $table->double('total_amount');
            $table->dateTime('transaction_date');
            $table->uuid('transaction_category_id');
            $table->string('transaction_remarks');
            $table->string('status')->default('PENDING');
            $table->string('bpi_reference');
            $table->longText('transaction_response');
            $table->uuid('user_created');
            $table->uuid('user_updated');
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
        Schema::dropIfExists('in_add_money_bpi');
    }
}
