<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('entity_id', 50);
            $table->uuid('user_account_id');
            $table->string('title', 10);
            $table->string('lastName', 50);
            $table->string('firstname', 50);
            $table->string('middlename', 50);
            $table->string('name_extension', 50);
            $table->date('birthdate');
            $table->string('place_of_birth', 50);
            $table->uuid('marital_status_id');
            $table->uuid('nationality_id');
            $table->string('encoded_nationality', 50);
            $table->string('occupation', 50);
            $table->string('house_no_street', 50);
            $table->string('city', 50);
            $table->string('provice_state', 50);
            $table->string('municipality', 50);
            $table->uuid('country_id');
            $table->string('postal_code', 5);
            $table->uuid('nature_of_work_id');
            $table->string('encoded_nature_of_work', 50);
            $table->uuid('source_of_fund_id');
            $table->string('encoded_source_of_fund', 50);
            $table->string('mother_maidenname', 50);
            $table->uuid('currency_id');
            $table->uuid('signup_host_id');
            $table->string('verification_status', 10);
            $table->string('user_account_status', 10);
            $table->string('emergency_lock_status', 10);
            $table->string('report_exception_status', 10);
            $table->uuid('user_created');
            $table->uuid('user_updated');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->unique(array('lastName', 'firstname', 'middlename'));

            $table->foreign('user_account_id')->references('id')->on('user_accounts');
            $table->foreign('marital_status_id')->references('id')->on('marital_status');
            $table->foreign('nationality_id')->references('id')->on('nationalities');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('nature_of_work_id')->references('id')->on('natures_of_work');
            $table->foreign('source_of_fund_id')->references('id')->on('source_of_funds');
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->foreign('signup_host_id')->references('id')->on('signup_hosts');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_details');
    }
}
