<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInAddMoneyEcPaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_add_money_ec_pays', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_account_id');
            $table->string('reference_number', 50)->nullable();
            $table->decimal('amount', 10, 3);
            $table->decimal('service_fee', 18, 6)->nullable();
            $table->string('service_fee_id', 36)->nullable();
            $table->decimal('total_amount', 18, 6)->nullable();
            $table->string('ec_pay_reference_number', 100);
            $table->datetime('expiry_date')->nullable();
            $table->datetime('transaction_date')->nullable();
            $table->uuid('transction_category_id');
            $table->string('transaction_remarks', 350);
            $table->string('status', 20)->default('pending');
            $table->uuid('user_created');
            $table->uuid('user_updated');
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
        Schema::dropIfExists('in_add_money_ec_pays');
    }
}
