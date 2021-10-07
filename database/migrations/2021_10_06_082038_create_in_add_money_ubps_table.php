<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInAddMoneyUbpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_add_money_ubps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_account_id');
            $table->string('reference_number', 20);
            $table->string('provider_reference_number', 20);
            $table->decimal('amount', 10, 3);
            $table->decimal('service_fee', 8, 3);
            $table->uuid('service_fee_id');
            $table->decimal('total_amount', 10, 3);
            $table->string('status', 10);
            $table->json('transaction_response');
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
        Schema::dropIfExists('in_add_money_ubps');
    }
}
