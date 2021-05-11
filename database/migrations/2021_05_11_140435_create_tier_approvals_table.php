<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTierApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tier_approvals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_account_id')->references('id')
                ->on('user_accounts')
                ->onDelete('restrict');
            $table->uuid('request_tier_id')->references('id')
                ->on('tiers')
                ->onDelete('restrict');
            $table->string('status', 20);
            $table->string('remarks', 50);
            $table->uuid('user_created')->references('id')
                ->on('user_accounts')
                ->onDelete('restrict');
            $table->uuid('user_updated')->references('id')
                ->on('user_accounts')
                ->onDelete('restrict');
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
        Schema::dropIfExists('tier_approvals');
    }
}
