<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTempUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('temp_user_details', function (Blueprint $table) {
            $table->string('encoded_nationality')->nullable()->change();
            $table->string('encoded_nature_of_work')->nullable()->change();
            $table->string('encoded_source_of_fund')->nullable()->change();
            $table->string('mobile_number')->nullable()->change();
            $table->string('email')->nullable()->change();

            $table->string('guardian_name')->nullable();  //varchar(50)
            $table->string('guardian_mobile_number')->nullable();  //varchar(50)
            $table->boolean('is_accept_parental_consent')->nullable();  //varchar(50)
            $table->boolean('contact_no')->nullable();  //varchar(50)
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
            $table->dropColumn('guardian_name');
            $table->dropColumn('guardian_mobile_number');
            $table->dropColumn('is_accept_parental_consent');
            $table->dropColumn('contact_no');
        });
    }
}
