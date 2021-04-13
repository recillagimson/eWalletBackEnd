<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tiers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->double('daily_limit', 10, 6);
            $table->double('daily_threshold', 10, 6);
            $table->double('monthly_limit', 10, 6);
            $table->double('monthly_threshold', 10, 6);
            $table->boolean('status');
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
        Schema::dropIfExists('tiers');
    }
}
