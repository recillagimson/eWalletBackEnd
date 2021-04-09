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
            $table->uuid('id')->unique();
            $table->uuid('user_account_id');
            $table->string('reference_number', 50);
            $table->string('namespace', 100);
            $table->timestamp('transaction_date');
            $table->string('remarks', 200);
            $table->uuid('user_created');
            $table->uuid('user_updated')->nullable();
            $table->timestamps();
            $table->timestamp('expires_at')->nullable();
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
