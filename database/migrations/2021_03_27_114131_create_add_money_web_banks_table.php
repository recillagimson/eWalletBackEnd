<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddMoneyWebBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('add_money_web_banks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_account_id');
            $table->uuid('user_saved_bank_id')->unique();
            $table->string('reference_number', 50)->unique();
            $table->double('amount', 10, 3);
            $table->double('service_fee', 10, 3);
            $table->uuid('service_fee_id');
            $table->double('total_amount', 10, 3);
            $table->string('dragonpay_reference', 50);
            $table->timestamp('transaction_date');
            $table->uuid('transaction_category_id');
            $table->string('transaction_remarks', 50);
            $table->uuid('user_created')->nullable();
            $table->uuid('user_updated')->nullable();
            $table->timestamp('expires_at')->nullable();
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
        Schema::dropIfExists('add_money_web_banks');
    }
}
