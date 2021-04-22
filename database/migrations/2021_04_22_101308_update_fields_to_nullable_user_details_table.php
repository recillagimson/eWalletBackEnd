<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFieldsToNullableUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('entity_id', 50)->nullable()->change();
            $table->string('title', 10)->nullable()->change();
            $table->string('name_extension', 50)->nullable()->change();
            $table->uuid('nationality_id')->nullable()->change();
            $table->string('encoded_nationality', 50)->nullable()->change();
            $table->uuid('nature_of_work_id')->nullable()->change();
            $table->string('encoded_nature_of_work', 50)->nullable()->change();
            $table->uuid('source_of_fund_id')->nullable()->change();
            $table->string('encoded_source_of_fund', 50)->nullable()->change();
            $table->string('verification_status', 10)->nullable()->change();
            $table->string('emergency_lock_status', 10)->nullable()->change();
            $table->string('report_exception_status', 10)->nullable()->change();
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
            $table->string('entity_id', 50)->change();
            $table->string('title', 10)->change();
            $table->string('name_extension', 50)->change();
            $table->uuid('nationality_id')->change();
            $table->string('encoded_nationality', 50)->change();
            $table->uuid('nature_of_work_id')->change();
            $table->string('encoded_nature_of_work', 50)->change();
            $table->uuid('source_of_fund_id')->change();
            $table->string('encoded_source_of_fund', 50)->change();
            $table->string('verification_status', 10)->change();
            $table->string('emergency_lock_status', 10)->change();
            $table->string('report_exception_status', 10)->change();
        });
    }
}
