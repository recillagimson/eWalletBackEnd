<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyOutPayMerchantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('out_pay_merchants', function (Blueprint $table) {
            $table->string('merchant_account_number', 50)->nullable()->change();
            $table->string('remarks')->nullable()->change();
            $table->string('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('out_pay_merchants', function (Blueprint $table) {
            $table->string('merchant_account_number', 50)->change();
            $table->string('remarks')->change();
            $table->string('description')->change();
        });
    }
}
