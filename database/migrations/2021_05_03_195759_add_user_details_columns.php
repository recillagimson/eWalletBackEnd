<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserDetailsColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('guardian_name', 50)->nullable();
            $table->string('guardian_mobile_number', 50)->nullable();
            $table->tinyInteger('is_accept_parental_consent')->nullable()->default(0);
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
            $table->dropColumn('guardian_name');
            $table->dropColumn('guardian_mobile_number');
            $table->dropColumn('is_accept_parental_consent');
        });
    }
}
