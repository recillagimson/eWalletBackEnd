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
            $table->uuid('id')->primary();
            $table->uuid('tier_id')->references('id')->on('tiers')->onDelete('restrict');
            $table->uuid('transaction_category_id')->references('id')->on('transaction_categories')->onDelete('restrict');;
            $table->dateTime('implementation_date');
            $table->uuid('user_created')->references('id')->on('user_accounts')->onDelete('restrict');
            $table->uuid('user_updated')->references('id')->on('user_accounts')->onDelete('restrict');
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
        Schema::dropIfExists('service_fees');
    }
}
