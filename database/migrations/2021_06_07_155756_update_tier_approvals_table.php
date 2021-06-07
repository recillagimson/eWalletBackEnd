<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTierApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tier_approvals', function (Blueprint $table) {
            $table->string('transaction_number', 50)->after('id');
            $table->dateTime('approved_date')->after('approved_by')->nullable();
            $table->dateTime('declined_date')->after('declined_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tier_approvals', function (Blueprint $table) {
            $table->dropColumn('transaction_number');
            $table->dropColumn('approved_date');
            $table->dropColumn('declined_date');
        });
    }
}
