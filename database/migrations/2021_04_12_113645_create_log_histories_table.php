<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_account_id')->references('id')->on('user_accounts')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('reference_number')->nullable();
            $table->string('squidpay_module')->nullable();
            $table->string('namespace')->nullable();
            $table->dateTime('transaction_date');
            $table->string('remarks');
            $table->string('operation')->nullable();
            $table->uuid('user_created')->references('id')->on('user_accounts')->onDelete('restrict');
            $table->uuid('user_updated')->references('id')->on('user_accounts')->onDelete('restrict');
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
        Schema::dropIfExists('log_histories');
    }
}
