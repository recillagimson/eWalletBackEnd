<?php

use App\Enums\AccountTiers;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NonNullableTierIdWithDefaultInUserAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_accounts', function (Blueprint $table) {
            $table->uuid('tier_id')->default(AccountTiers::tier1)->change();
            $table->foreign('tier_id')->references('id')->on('tiers')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_accounts', function (Blueprint $table) {
            $table->uuid('tier_id')->nullable();
        });
    }
}
