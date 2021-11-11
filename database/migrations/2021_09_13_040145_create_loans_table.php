<?php

use Illuminate\Database\Migrations\Migration;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('loans', function (Blueprint $table) {
//            $table->uuid('id')->primary();
//            $table->uuid('user_account_id');
//            $table->string('reference_number', 50);
//            $table->string('status');
//            $table->uuid('user_created');
//            $table->uuid('user_updated');
//            $table->timestamps();
//            $table->softDeletes();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::dropIfExists('loans');
    }
}
