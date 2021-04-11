<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePinCodeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pin_code_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_account_id');
            $table->string('pin_code');
            $table->boolean('expired')->default(false);
            $table->uuid('user_created');
            $table->uuid('user_updated')->nullable();
            $table->timestamps();

            $table->foreign('user_account_id')->references('id')->on('user_accounts')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('user_created')->references('id')->on('user_accounts')
                ->onDelete('restrict');

            $table->foreign('user_updated')->references('id')->on('user_accounts')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pin_code_histories');
    }
}
