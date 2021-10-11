<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUbpAccountTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ubp_account_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_account_id')->unique();
            $table->string('token_type', 20);
            $table->text('access_token');
            $table->string('metadata', 255);
            $table->dateTime('expires_in');
            $table->dateTime('consented_on');
            $table->string('scope');
            $table->text('refresh_token');
            $table->dateTime('refresh_token_expiration');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ubp_account_tokens');
    }
}
