<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrcrMemoControlNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drcr_memo_control_numbers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('control_number');
            $table->integer('num_rows')->nullable();
            $table->string('status')->nullable();
            $table->uuid('user_created');
            $table->uuid('user_updated');
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
        Schema::dropIfExists('drcr_memo_control_numbers');
    }
}
