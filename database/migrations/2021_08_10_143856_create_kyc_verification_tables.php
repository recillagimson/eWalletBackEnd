<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKycVerificationTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kyc_verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_account_id');
            $table->string('request_id');
            $table->string('application_id');
            $table->text('hv_response')->nullable();
            $table->string('hv_result')->nullable();
            $table->string('status')->default('PENDING');
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
        Schema::dropIfExists('kyc_verifications');
    }
}
