<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_user_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('transaction_number');  //varchar(50)
            // $table->string('entity_id')->nullable();  //varchar nullable
            $table->uuid('user_account_id');
            // $table->string('title');  //varchar(10) nullable
            $table->string('last_name');  //varchar(50) 
            $table->string('first_name');  //varchar(50) 
            $table->string('middle_name');  //varchar(50) 
            $table->string('name_extension');  //varchar(50) nullable
            $table->uuid('nationality_id');
            $table->string('encoded_nationality');  //varchar(50)
            $table->date('birth_date');  //date 
            $table->string('house_no_street');  //varchar(50) 
            $table->string('provice_state');  //varchar(100) 
            $table->string('city');  //varchar(100) 
            // $table->string('municipality');  //varchar(100) 
            $table->string('postal_code');  //varchar(5) 
            $table->uuid('country_id')->nullable();
            $table->string('place_of_birth');  //varchar(50)
            $table->string('mother_maidenname');  //varchar(50)
            $table->uuid('marital_status_id');
            $table->string('occupation');  // varchar(50) 
            $table->uuid('nature_of_work_id');
            $table->string('encoded_nature_of_work');  //varchar(50)
            $table->uuid('source_of_fund_id');
            $table->string('encoded_source_of_fund');  //varchar(50)
            // $table->uuid('currency_id')->references('id')->on('currencies');  //varchar(36) FK >- currencies.id
            // $table->uuid('signup_host_id')->references('id')->on('signup_hosts');  //varchar(36) FK >- signup_hosts.id
            // $table->string('guardian_name');  //varchar(50)
            // $table->string('guardian_mobile_number');  //varchar(50)
            $table->string('employer');  //varchar(50)
            $table->string('mobile_number');  //varchar(50)
            $table->string('email');  //varchar(50)
            $table->string('status');  //varchar(20)
            $table->string('remarks');  //varchar(50)
            $table->string('reviewed_by')->references('id')->on('user_accounts');  //varchar(36) FK >- user_accounts.id
            $table->dateTime('reviewed_date');  //datetime
            $table->string('approved_by')->nullable()->references('id')->on('user_accounts');  //varchar(36) FK >- user_accounts.id
            $table->dateTime('approved_date')->nullable();  //datetime
            $table->string('declined_by')->nullable()->references('id')->on('user_accounts');  //varchar(36) FK >- user_accounts.id
            $table->dateTime('declined_date')->nullable();  //datetime
            $table->uuid('user_created');
            $table->uuid('user_updated');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_account_id')->references('id')->on('user_accounts');  //varchar(36) FK >- user_accounts.id
            $table->foreign('nationality_id')->references('id')->on('nationalities');  //varchar(36)  FK >- nationalities.id
            $table->foreign('country_id')->references('id')->on('countries');  //varchar(36) FK >- countries.id
            $table->foreign('marital_status_id')->references('id')->on('marital_status');  //varchar(36) FK >- marital_status.id
            $table->foreign('nature_of_work_id')->references('id')->on('natures_of_work');  //varchar(36) FK >- natures_of_work.id
            $table->foreign('source_of_fund_id')->references('id')->on('source_of_funds');  //varchar(36) FK >- source_of_funds.id
            $table->foreign('user_created')->references('id')->on('user_accounts');
            $table->foreign('user_updated')->references('id')->on('user_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temp_user_details');
    }
}
