<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOutPaybillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('out_pay_bills', function (Blueprint $table) {
            $table->renameColumn('transction_category_id', 'transaction_category_id');
            $table->string('status', 20)->change();
            $table->decimal('other_charges', 19, 6)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('out_pay_bills', function (Blueprint $table) {
            $table->boolean('status')->change();
        });
    }
}
