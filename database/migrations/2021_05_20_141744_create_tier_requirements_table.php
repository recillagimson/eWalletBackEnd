<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTierRequirementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tier_requirements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tier_id',36)->references('id')->on('tiers');
            $table->string('name',100);
            $table->string('requirement_type',50)->nullable();
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
        Schema::dropIfExists('tier_requirements');
    }
}
