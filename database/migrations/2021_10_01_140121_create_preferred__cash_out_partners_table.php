<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreferredCashOutPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preferred_cash_out_partners', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('description');
            $table->string('status');
            $table->uuid('user_created');
            $table->uuid('user_updated');
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
        Schema::dropIfExists('preferred_cash_out_partners');
    }
}
