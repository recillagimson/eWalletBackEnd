<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTransctionCategoryIdInInAddMoneyFromBank extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_add_money_from_bank', function (Blueprint $table) {
            $table->renameColumn('transction_category_id', 'transaction_category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_add_money_from_bank', function (Blueprint $table) {
            $table->renameColumn('transaction_category_id', 'transction_category_id');
        });
    }
}
