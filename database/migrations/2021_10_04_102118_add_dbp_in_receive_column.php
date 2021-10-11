<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDbpInReceiveColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_receive_from_dbp', function (Blueprint $table) {
            $table->string('funding_currency')->nullable();
            $table->string('remittance_date')->nullable();
            $table->string('service_code')->nullable();
            $table->string('outlet_name')->nullable();
            $table->string('beneficiary_name_1')->nullable();
            $table->string('beneficiary_name_2')->nullable();
            $table->string('beneficiary_name_3')->nullable();
            $table->string('beneficiary_address_1')->nullable();
            $table->string('beneficiary_address_2')->nullable();
            $table->string('beneficiary_address_3')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('message')->nullable();
            $table->string('remitter_name_1')->nullable();
            $table->string('remitter_name_2')->nullable();
            $table->string('remitter_address_1')->nullable();
            $table->string('remitter_address_2')->nullable();
        });
    }



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_receive_from_dbp', function (Blueprint $table) {
            $table->dropColumn('funding_currency');
            $table->dropColumn('remittance_date');
            $table->dropColumn('service_code');
            $table->dropColumn('total_amount');
            $table->dropColumn('outlet_name');
            $table->dropColumn('beneficiary_name_1');
            $table->dropColumn('beneficiary_name_2');
            $table->dropColumn('beneficiary_name_3');
            $table->dropColumn('beneficiary_address_1');
            $table->dropColumn('beneficiary_address_2');
            $table->dropColumn('beneficiary_address_3');
            $table->dropColumn('mobile_number');
            $table->dropColumn('message');
            $table->dropColumn('remitter_name_1');
            $table->dropColumn('remitter_name_2');
            $table->dropColumn('remitter_address_1');
            $table->dropColumn('remitter_address_2');
        });
    }
}
