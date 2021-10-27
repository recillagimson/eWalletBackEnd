<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInDisburesmentDbps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_disbursement_dbps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_account_id');
            $table->string('reference_number');
            $table->string('out_disbursement_dbps_reference_number');
            $table->double('total_amount');
            $table->string('status')->default('PENDING');
            $table->dateTime('transaction_date');
            $table->uuid('transaction_category_id');
            $table->string('transaction_remarks');
            $table->uuid('disbursed_by');
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
        Schema::dropIfExists('in_disbursement_dbps');
    }
}
