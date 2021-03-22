<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('userAccounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email', 50)->unique();
            $table->string('mobileNumber', 20)->unique();
            $table->string('password');
            $table->boolean('swiVerificationStatus')->default(false);
            $table->boolean('swiUserAccountStatus')->default(false);
            $table->boolean('swiUserLoginStatus')->default(false);
            $table->boolean('swiReportExceptionStatus')->default(false);
            $table->boolean('isAdmin')->default(false);
            $table->string('status', 50);
            $table->string('pinCode', 50);
            $table->uuid('userCreated')->nullable();
            $table->uuid('userUpdated')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('userAccounts');
    }
}
