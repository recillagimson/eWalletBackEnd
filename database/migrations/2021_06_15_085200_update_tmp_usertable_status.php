<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTmpUsertableStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('temp_user_details', function (Blueprint $table) {
            $table->string('status')->default('PENDING')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('temp_user_details', function (Blueprint $table) {
            $table->string('status')->default('PENDING')->change();
        });
    }
}
