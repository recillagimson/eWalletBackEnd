<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRateColumnFromForeignExchangeRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('foreign_exchange_rates', function (Blueprint $table) {
            //
            $table->renameColumn('from', 'code');
            $table->float('rate', 15, 10)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('foreign_exchange_rates', function (Blueprint $table) {
            //
            $table->renameColumn('code', 'from');
            $table->decimal('rate', 8, 2)->change();
        });
    }
}
