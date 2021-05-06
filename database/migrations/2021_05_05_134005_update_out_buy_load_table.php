<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOutBuyLoadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('out_buy_loads', function (Blueprint $table) {
            $table->string('reference_number')->nullable();
            $table->string('atm_reference_number')->nullable();
            $table->string('transaction_remarks')->nullable()->change();
            $table->uuid('prepaid_load_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('out_buy_loads', function (Blueprint $table) {
            $table->dropColumn('reference_number');
            $table->dropColumn('atm_reference_number');
            $table->string('transaction_remarks')->change();
            $table->uuid('prepaid_load_id')->change();
        });
    }
}
