<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetFieldsToNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('house_no_street', 50)->nullable()->change();
            $table->string('provice_state', 50)->nullable()->change();
            $table->string('city', 50)->nullable()->change();
            $table->string('postal_code', 50)->nullable()->change();
            $table->string('place_of_birth', 50)->nullable()->change();
            $table->string('mother_maidenname', 50)->nullable()->change();
            //$table->string('marital_status_id', 50)->nullable()->change();
            $table->string('postal_code', 50)->nullable()->change();
            //$table->string('nature_of_work_id', 50)->nullable()->change();
            $table->string('contact_no', 50)->nullable();
            //$table->string('source_of_fund_id', 50)->nullable()->change();
            $table->string('occupation', 50)->nullable()->change();
            $table->string('employer', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('house_no_street', 50)->change();
            $table->string('provice_state', 50)->change();
            $table->string('city', 50)->change();
            $table->string('postal_code', 50)->change();
            $table->string('place_of_birth', 50)->change();
            $table->string('mother_maidenname', 50)->change();
            //$table->string('marital_status_id', 50)->change();
            $table->string('postal_code', 50)->change();
           // $table->string('nature_of_work_id', 50)->change();
            $table->string('contact_no', 50);
            //$table->string('source_of_fund_id', 50)->change();
            $table->string('occupation', 50)->change();
            $table->string('employer', 50);
        });
    }
}
