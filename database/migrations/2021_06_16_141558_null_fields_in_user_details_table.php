<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NullFieldsInUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->date('birth_date')->nullable()->change();
            $table->uuid('marital_status_id')->nullable()->change();
            $table->string('municipality', 50)->nullable()->change();
            $table->uuid('country_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->date('birth_date')->change();
            $table->uuid('marital_status_id')->change();
            $table->string('municipality', 50)->change();
            $table->uuid('country_id')->change();
        });
    }
}
