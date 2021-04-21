<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_fees', function (Blueprint $table) {
            $table->uuid('id')->unique();
            $table->string('old_service_fee_id', 50);
            $table->integer('tier');
            $table->uuid('transaction_category_id');
            $table->string('name', 50);
            $table->decimal('amount', 10, 3);
            $table->timestamp('implementation_date');
            $table->uuid('user_created');
            $table->uuid('user_updated');
            $table->timestamps();
            $table->softDeletes();
        });
    }php 

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_fees');
    }
}
