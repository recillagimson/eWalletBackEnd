<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTierServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tier_services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tier_id',36)->references('id')->on('tiers');
            $table->uuid('transaction_category_id')->references('id')->on('transaction_categories');
            $table->boolean('is_accessible');
            $table->boolean('status')->nullable();
            $table->uuid('user_created')->nullable();
            $table->uuid('user_updated')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tier_services');
    }
}
